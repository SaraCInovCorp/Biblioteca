<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookRequest;
use App\Models\BookRequestItem;
use App\Models\Livro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PedidoConfirmacaoMail;
use App\Mail\PedidoNotificacaoMail;
use App\Models\User;

class BookRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $query = BookRequest::with(['items.livro', 'user']);
        } else {
            $query = BookRequest::with(['items.livro'])
                ->where('user_id', $user->id);
        }

        $bookRequests = $query->paginate(10);

        $indicators = [];
        if ($user->role === 'admin') {
            $indicators = [
                'requisicoes_ativas'   => BookRequest::where('ativo', true)->count(),
                'requisicoes_30dias'   => BookRequest::where('data_inicio', '>=', now()->subDays(30))->count(),
                'livros_entregues_hoje'=> BookRequestItem::whereDate('data_real_entrega', now())->count(),
            ];
        }

        return view('requisicoes.index', compact('bookRequests', 'indicators'));
    }

    public function create()
    {
        \Log::info('Entrou no método create do BookRequestController');
        $user = Auth::user(); // já vai existir
        $livros = Livro::where('status', 'disponivel')->get();
        $users = $user->role === 'admin'
            ? User::where('role', 'cidadao')->get()
            : null;

        return view('requisicoes.create', compact('livros', 'users', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user(); // já vai existir

        $validated = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim'    => 'required|date|after_or_equal:data_inicio',
            'notas'       => 'nullable|string',
            'ativo'       => 'required|boolean',
            'items'       => 'required|array|min:1',
            'items.*.livro_id' => 'required|exists:livros,id',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        
            if ($user->role === 'admin') {
                $requestUserId = $validated['user_id'] ?? $user->id;
            } else {
                $requestUserId = $user->id;
            }

        // Validação do limite de livros (apenas cidadão)
        if ($user->role === 'cidadao') {
            $countReqLivros = BookRequestItem::whereHas('bookRequest', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('ativo', true);
            })->count();

            if ($countReqLivros + count($validated['items']) > 3) {
                return back()->withErrors(['items' => 'Você já possui 3 livros requisitados em simultâneo.']);
            }
        }

        // Verificar disponibilidade dos livros
        foreach ($validated['items'] as $item) {
            $livro = Livro::findOrFail($item['livro_id']);
            if ($livro->status !== 'disponivel') {
                return back()->withErrors(['items' => "O livro '{$livro->titulo}' não está disponível para requisição."]);
            }
        }

        \DB::beginTransaction();

        try {
            // Criar requisição
            $bookRequest = BookRequest::create([
                'user_id'     => $requestUserId,
                'data_inicio' => $validated['data_inicio'],
                'data_fim'    => $validated['data_fim'],
                'notas'       => $validated['notas'] ?? null,
                'ativo'       => $validated['ativo'],
            ]);

            // Criar itens
            foreach ($validated['items'] as $item) {
                $bookRequest->items()->create([
                    'livro_id'         => $item['livro_id'],
                    'data_real_entrega'=> null,
                    'dias_decorridos'  => null,
                    'status'           => 'realizada',
                ]);

                $livro = Livro::find($item['livro_id']);
                $livro->status = 'requisitado';
                $livro->save();
            }

            \DB::commit();

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar requisição: ' . $e->getMessage()]);
        }

        // Emails (opcional)
        // Mail::to($bookRequest->user->email)->send(new PedidoConfirmacaoMail($bookRequest));
        // Mail::to($admins)->send(new PedidoNotificacaoMail($bookRequest));

        return redirect()->route('requisicoes.index')->with('success', 'Requisição criada com sucesso!');
    }


    public function show(BookRequest $bookRequest)
    {
        $this->authorize('view', $bookRequest);

        $bookRequest->load(['items.livro', 'user']);

        return view('requisicoes.show', compact('bookRequest'));
    }

    public function edit(BookRequest $bookRequest)
    {
        $this->authorize('update', $bookRequest);

        $livros = Livro::where('status', 'disponivel')
            ->orWhereHas('bookRequestItems', function ($q) use ($bookRequest) {
                $q->where('book_request_id', $bookRequest->id);
            })->get();

        return view('requisicoes.edit', compact('bookRequest', 'livros'));
    }

    public function update(Request $request, BookRequest $bookRequest)
    {
        $this->authorize('update', $bookRequest);

        $validated = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim'    => 'required|date|after_or_equal:data_inicio',
            'notas'       => 'nullable|string',
            'ativo'       => 'required|boolean',
        ]);

        $bookRequest->update($validated);

        return redirect()->route('requisicoes.show', $bookRequest)->with('success', 'Requisição atualizada com sucesso!');
    }

    public function searchUsers(Request $request)
    {
        //$this->authorize('viewAny', User::class); // opcional, controle de acesso
        $user = $request->user();

        if ($user->role !== 'admin') {
            abort(403, 'Acesso negado');
        }

        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('role', 'cidadao')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    public function searchLivros(Request $request)
    {
        $query = $request->get('q', '');

        if(strlen($query) < 2){
            return response()->json([]);
        }

        $livros = Livro::where('status', 'disponivel')
            ->where(function ($q) use ($query) {
                $q->where('titulo', 'like', "%{$query}%")
                ->orWhere('autor', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'titulo', 'autor']);

        return response()->json($livros);
    }

}
