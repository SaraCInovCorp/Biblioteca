<?php

namespace App\Http\Controllers;

use App\Models\LivroWaitingList;
use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class LivroWaitingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $livros = Livro::where('status', 'disponivel')
                ->whereHas('waitingList', function ($q) {
                    $q->where('ativo', true);
                })
                ->with(['waitingList' => function ($q) {
                    $q->where('ativo', true)->with('user');
                }])
                ->get();

            return view('waiting-list.index', compact('livros'));
        } else {
            $waitingList = LivroWaitingList::with('livro')
                ->where('user_id', $user->id)
                ->where('ativo', true)
                ->get();

            return view('waiting-list.index', compact('waitingList'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Livro $livro)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        $existe = LivroWaitingList::where('livro_id', $livro->id)
            ->where('user_id', $user->id)
            ->where('ativo', true)
            ->exists();

        if ($existe) {
            return response()->json([
                'error' => 'Você já está inscrito para ser avisado quando este livro estiver disponível.'
            ], 422);
        }

        $item = LivroWaitingList::create([
            'livro_id' => $livro->id,
            'user_id' => $user->id,
            'ativo' => true,
            'notificado_em' => null,
        ]);

        activity()
        ->causedBy($user)
        ->performedOn($item)
        ->event('store')
        ->useLog('store-waitinglist')
        ->withProperties([
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ])
        ->log('Inscrição na lista de espera');

        return response()->json([
            'success' => 'Inscrição realizada com sucesso! Você será notificado.'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LivroWaitingList $livroWaitingList)
    {
        $user = auth()->user();

        if ($livroWaitingList->user_id !== $user->id) {
            abort(403);
        }

        $livroWaitingList->ativo = false;
        $livroWaitingList->save();

        activity()
        ->causedBy($user)
        ->performedOn($livroWaitingList)
        ->event('destroy')
        ->useLog('destroy-waitinglist')
        ->withProperties([
            'ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ])
        ->log('Cancelamento de inscrição na lista de espera');

        return back()->with('success', 'Inscrição cancelada com sucesso.');
    }
}
