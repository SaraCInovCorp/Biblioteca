<?php

namespace App\Http\Controllers;

use App\Models\LivroWaitingList;
use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\User;

class LivroWaitingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $waitingList = LivroWaitingList::with('livro')
            ->where('user_id', $user->id)
            ->where('ativo', true)
            ->get();

        return view('livro_waiting_list.index', compact('waitingList'));
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

        LivroWaitingList::create([
            'livro_id' => $livro->id,
            'user_id' => $user->id,
            'ativo' => true,
            'notificado_em' => null,
        ]);

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

        return back()->with('success', 'Inscrição cancelada com sucesso.');
    }
}
