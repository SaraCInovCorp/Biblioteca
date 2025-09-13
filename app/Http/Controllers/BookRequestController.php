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
use App\Models\Autor;
use Carbon\Carbon;

class BookRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filtro = $request->get('filtro', '');
        $search = $request->input('search');
        $statusFiltro = $request->input('status'); // ex: 'ativa' ou 'inativa'
        $dataInicioFiltro = $request->input('data_inicio');
        $dataFimFiltro = $request->input('data_fim');
        $dataRealEntregaFiltro = $request->input('data_real_entrega');
        $userIdFiltro = $request->input('user_id');
        $itemStatusFiltro = $request->input('item_status');

        $queryBase = BookRequest::with(['items', 'items.livro', 'user']);

        if ($user->role !== 'admin') {
            $queryBase->where('user_id', $user->id);
        }

        $ativasQuery = (clone $queryBase)
            ->where('ativo', true)
            ->whereHas('items', function ($q) {
                $q->whereIn('status', ['realizada', 'nao_entregue']);
            })
            ->orderBy('data_inicio', 'desc');

        $passadasQuery = (clone $queryBase)
            ->where('ativo', false)
            ->orderBy('data_inicio', 'desc');

        $ativas = collect();
        $passadas = collect();
        $bookRequests = collect();

        switch ($filtro) {
            case 'ativas':
                $bookRequests = $ativasQuery->paginate(10);
                break;

            case '30dias':
                $bookRequests = $queryBase->where('data_inicio', '>=', now()->subDays(30))
                    ->orderBy('data_inicio', 'desc')
                    ->paginate(10);
                break;

            case 'entregues_hoje':
                $bookRequests = $queryBase->whereHas('items', fn($q) => $q->whereDate('data_real_entrega', now()))
                    ->orderBy('data_inicio', 'desc')
                    ->paginate(10);
                break;

            default:
                if ($search || $itemStatusFiltro || $statusFiltro || $dataInicioFiltro || $dataFimFiltro || $dataRealEntregaFiltro || ($user->role === 'admin' && $userIdFiltro)) {
                    $query = BookRequest::with(['items', 'items.livro', 'user']);

                    if ($search) {
                        $query->whereHas('items.livro', fn($q) =>
                            $q->where('titulo', 'like', "%{$search}%")
                            ->orWhereHas('autores', fn($q2) =>
                                $q2->where('nome', 'like', "%{$search}%")
                            )
                        );
                    }
                    \Log::info($itemStatusFiltro);
                    if ($itemStatusFiltro) {
                        $query->whereHas('items', fn($q) =>
                            $q->where('status', $itemStatusFiltro)
                        );
                    }
                    

                    if ($statusFiltro === 'ativa') {
                        $query->where('ativo', true);
                    } elseif ($statusFiltro === 'inativa') {
                        $query->where('ativo', false);
                    }

                    if ($dataInicioFiltro) {
                        $query->whereDate('data_inicio', '=', $dataInicioFiltro);
                    }

                    if ($dataFimFiltro) {
                        $query->whereDate('data_fim', '=', $dataFimFiltro);
                    }

                    if ($dataRealEntregaFiltro) {
                        $query->whereHas('items', fn($q) => $q->whereDate('data_real_entrega', '=', $dataRealEntregaFiltro));
                    }

                    if ($user->role === 'admin' && $userIdFiltro) {
                        $query->where('user_id', $userIdFiltro);
                    } elseif ($user->role !== 'admin') {
                        $query->where('user_id', $user->id);
                    }

                    $bookRequests = $query->orderBy('data_inicio', 'desc')->paginate(10);
                    \Log::info($query->toSql(), $query->getBindings());
                    \Log::info($bookRequests);
                } else {
                    // Nenhum filtro avançado: usar listas padrão para exibir
                    $ativas = $ativasQuery->get();
                    $passadas = $passadasQuery->get();
                }
                break;
        }

        $indicators = [];
        if ($user->role === 'admin') {
            $indicators = [
                'requisicoes_ativas' => $ativasQuery->count(),
                'requisicoes_30dias' => $queryBase->where('data_inicio', '>=', now()->subDays(30))->count(),
                'livros_entregues_hoje' => BookRequestItem::whereDate('data_real_entrega', now())->count(),
            ];
        }

        $users = $user->role === 'admin' ? User::orderBy('name')->get() : collect();

        return view('requisicoes.index', compact(
            'ativas', 'passadas', 'bookRequests', 'filtro', 'indicators',
            'search', 'statusFiltro', 'dataInicioFiltro', 'dataFimFiltro', 'userIdFiltro', 'users', 'itemStatusFiltro', 'dataRealEntregaFiltro'
        ));
    }

    public function create()
    {
        //\Log::info('Entrou no método create do BookRequestController');
        $user = Auth::user(); // já vai existir
        $livros = Livro::where('status', 'disponivel')->get();
        $users = $user->role === 'admin'
            ? User::where('role', 'cidadao')->get()
            : null;

        return view('requisicoes.create', compact('livros', 'users', 'user'));
    }

    public function store(Request $request)
    {
        
        $user = Auth::user(); 

        $validated = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim'    => 'required|date|after_or_equal:data_inicio',
            'notas'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.livro_id' => 'required|exists:livros,id',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        
            if ($user->role === 'admin') {
                $requestUserId = $validated['user_id'] ?? $user->id;
            } else {
                $requestUserId = $user->id;
            }

        
        if ($user->role === 'cidadao') {
            $countReqLivros = BookRequestItem::whereHas('bookRequest', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('ativo', true);
            })->count();

            if ($countReqLivros + count($validated['items']) > 3) {
                return back()->withErrors(['items' => 'Você já possui 3 livros requisitados em simultâneo.']);
            }
        }

        
        foreach ($validated['items'] as $item) {
            $livro = Livro::findOrFail($item['livro_id']);
            if ($livro->status !== 'disponivel') {
                return back()->withErrors(['items' => "O livro '{$livro->titulo}' não está disponível para requisição."]);
            }
        }

        if ($user->role === 'cidadao') {
            $dataInicio = Carbon::parse($validated['data_inicio']);
            if ($dataInicio->lt(Carbon::today())) {
                return back()->withErrors(['data_inicio' => 'Usuários cidadãos não podem criar requisições com data de início no passado.'])->withInput();
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
                'ativo'       => true,
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
            $request->session()->forget('book_request');


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
            'items'       => 'required|array|min:1',
            'items.*.livro_id' => 'required|exists:livros,id',
            'items.*.data_real_entrega' => 'nullable|after_or_equal:data_inicio',
            'items.*.dias_decorridos' => 'nullable',
            'items.*.status' => 'required',
        ]);

        $isAtivo = false;
        foreach ($validated['items'] as $item) {

                if (!$isAtivo) {
                    $isAtivo = ($item['status'] == 'realizada' || $item['status'] == 'nao_entregue');
                }

                $bookRequest->items()->update([
                    'livro_id'         => $item['livro_id'],
                    'data_real_entrega'=> $item['data_real_entrega'],
                    'dias_decorridos'  => $item['dias_decorridos'],
                    'status'           => $item['status'],
                ]);

                $livro = Livro::find($item['livro_id']);
                $livro->status = ($item['status'] != 'realizada' && $item['status'] != 'nao_entregue') ? 'disponivel' : $livro->status;
                $livro->save();
            }
            $validated['ativo'] = $isAtivo;

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

    public function destroy(BookRequest $bookRequest)
    {
        $this->authorize('delete', $bookRequest); // Se tiver política

        $now = now();

        if ($now->gte($bookRequest->data_inicio)) {
            return back()->withErrors(['error' => 'Não é possível cancelar a requisição após sua data de início.']);
        }

        \DB::beginTransaction();

        try {
            // Marca requisição como inativa
            $bookRequest->ativo = false;
            $bookRequest->save();

            // Atualiza status dos livros associados para "disponivel"
            foreach ($bookRequest->items as $item) {
                $livro = $item->livro;
                $livro->status = 'disponivel';
                $livro->save();

                // Opcional: atualizar status do item da requisição para "cancelada"
                $item->status = 'cancelada';
                $item->save();
            }

            \DB::commit();

            return redirect()->route('requisicoes.index')->with('success', 'Requisição cancelada com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao cancelar a requisição: ' . $e->getMessage()]);
        }
    }


}
