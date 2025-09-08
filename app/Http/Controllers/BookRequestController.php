<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookRequest;
use App\Models\BookRequestItem;
use App\Models\Livro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin pode ver todas requisições, com filtro opcional
            $query = BookRequest::with(['items.livro', 'user']);
        } else {
            // Cidadão vê só suas próprias requisições
            $query = BookRequest::with(['items.livro'])
                ->where('user_id', $user->id);
        }

        // Aplicar filtro de pesquisa no índice, por exemplo filtro por data, status, etc (opcional)

        $bookRequests = $query->paginate(10);

        // Indicadores topo (apenas admin)
        $indicators = [];
        if ($user->role === 'admin') {
            $indicators = [
                'requisicoes_ativas' => BookRequest::where('status', 'realizada')->count(),
                'requisicoes_30dias' => BookRequest::where('data_inicio', '>=', now()->subDays(30))->count(),
                'livros_entregues_hoje' => BookRequestItem::whereDate('data_real_entrega', now())->count(),
            ];
        }

        return view('book_requests.index', compact('bookRequests', 'indicators'));
    }

    public function create()
    {
        $user = Auth::user();

        $this->authorize('create', BookRequest::class);

        $livros = Livro::where('status', 'disponivel')->get();

        // Para admin, pode requisitar para qualquer cidadão, então lista usuários cidadãos
        // Para cidadão, requisita para si mesmo, não precisa passar usuários

        $users = $user->role === 'admin' ? \App\Models\User::where('role', 'cidadao')->get() : null;

        return view('book_requests.create', compact('livros', 'users', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $this->authorize('create', BookRequest::class);

        // Validações customizadas para regra de negócio

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'notas' => 'nullable|string',
            'ativo' => 'required|boolean',
            'items' => 'required|array|min:1',
            'items.*.livro_id' => 'required|exists:livros,id',
        ]);

        // Usuário cidadão pode só requisitar para si
        if ($user->role === 'cidadao' && $validated['user_id'] != $user->id) {
            abort(403, 'Você não tem permissão para requisitar para outro usuário.');
        }

        // Verificar se cidadão tem menos que 3 livros requisitados simultaneamente
        if ($user->role === 'cidadao') {
            $countReqLivros = BookRequestItem::whereHas('bookRequest', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('ativo', true);
            })->count();

            if ($countReqLivros + count($validated['items']) > 3) {
                return back()->withErrors(['items' => 'Você já possui 3 livros requisitados em simultâneo.']);
            }
        }

        // Verificar se todos livros estão disponíveis
        foreach ($validated['items'] as $item) {
            $livro = Livro::findOrFail($item['livro_id']);
            if ($livro->status !== 'disponivel') {
                return back()->withErrors(['items' => "O livro '{$livro->titulo}' não está disponível para requisição."]);
            }
        }

        // Criar a requisição
        $bookRequest = BookRequest::create([
            'user_id' => $validated['user_id'],
            'data_inicio' => $validated['data_inicio'],
            'data_fim' => $validated['data_fim'],
            'notas' => $validated['notas'] ?? null,
            'ativo' => $validated['ativo'],
            'status' => 'realizada',
        ]);

        // Criar os itens da requisição e atualizar status do livro para "requisitado"
        foreach ($validated['items'] as $item) {
            $bookRequest->items()->create([
                'livro_id' => $item['livro_id'],
                'data_real_entrega' => null,
                'dias_decorridos' => null,
                'status' => 'realizada',
            ]);

            $livro = Livro::find($item['livro_id']);
            $livro->status = 'requisitado';
            $livro->save();
        }

        // Enviar email notificação para Admins e para o cidadão solicitante (implementar Mailables)
        // Mail::to($cidadão_email)->send(new PedidoConfirmacaoMail($bookRequest));
        // Mail::to($lista_admins_emails)->send(new PedidoNotificacaoMail($bookRequest));

        return redirect()->route('book_requests.index')->with('success', 'Requisição criada com sucesso!');
    }

    public function show(BookRequest $bookRequest)
    {
        $this->authorize('view', $bookRequest);

        $bookRequest->load(['items.livro', 'user']);

        return view('book_requests.show', compact('bookRequest'));
    }

    public function edit(BookRequest $bookRequest)
    {
        $this->authorize('update', $bookRequest);

        $livros = Livro::where('status', 'disponivel')->orWhereHas('bookRequestItems', function ($q) use ($bookRequest) {
            $q->where('book_request_id', $bookRequest->id);
        })->get();

        return view('book_requests.edit', compact('bookRequest', 'livros'));
    }

    public function update(Request $request, BookRequest $bookRequest)
    {
        $this->authorize('update', $bookRequest);

        $validated = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'notas' => 'nullable|string',
            'ativo' => 'required|boolean',
            'status' => 'required|in:realizada,cancelada',
        ]);

        $bookRequest->update($validated);

        // Atualizar itens se necessário (exemplo simples)
        // Pode implementar lógica para atualizar itens, status de entrega etc.

        return redirect()->route('book_requests.show', $bookRequest)->with('success', 'Requisição atualizada com sucesso!');
    }
}

