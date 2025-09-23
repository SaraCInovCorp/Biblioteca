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
use App\Models\BookReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Mail\ReviewCreatedAdminNotification;

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
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'notas' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.livro_id' => 'required|exists:livros,id',
            'items.*.obs' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $requestUserId = $user->role === 'admin'
            ? ($validated['user_id'] ?? $user->id)
            : $user->id;

        if ($user->role === 'cidadao') {
            $countReqLivros = BookRequestItem::whereHas('bookRequest', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('ativo', true);
            })->count();

            if ($countReqLivros + count($validated['items']) > 3) {
                return back()->withErrors(['items' => 'Você já possui 3 livros requisitados em simultâneo.'])->withInput();
            }
        }

        foreach ($validated['items'] as $item) {
            $livro = Livro::findOrFail($item['livro_id']);
            if ($livro->status !== 'disponivel') {
                return back()->withErrors(['items' => "O livro '{$livro->titulo}' não está disponível para requisição."])->withInput();
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
            $bookRequest = BookRequest::create([
                'user_id' => $requestUserId,
                'data_inicio' => $validated['data_inicio'],
                'data_fim' => $validated['data_fim'],
                'lembrete_enviado_em' => null,
                'lembrete_enviado_para' => null,
                'notas' => $validated['notas'] ?? null,
                'ativo' => true,
            ]);

            foreach ($validated['items'] as $item) {
                $bookRequest->items()->create([
                    'livro_id' => $item['livro_id'],
                    'data_real_entrega' => null,
                    'dias_decorridos' => null,
                    'status' => 'realizada',
                    'obs' => $item['obs'] ?? null,
                ]);

                $livro = Livro::find($item['livro_id']);
                $livro->status = 'requisitado';
                $livro->save();
            }

            \DB::commit();
            $request->session()->forget('book_request');

            $bookRequest->load(['user', 'items.livro']);

            try {
                Mail::to($bookRequest->user->email)->send(new PedidoConfirmacaoMail($bookRequest));
            } catch (\Exception $e) {
                \Log::error("Erro ao enviar confirmação para usuário {$bookRequest->user->email}: " . $e->getMessage());
            }

            try {
                $admins = User::where('role', 'admin')->pluck('email')->toArray();

                if (!empty($admins)) {
                    Mail::to($admins)->send(new PedidoNotificacaoMail($bookRequest));
                }
            } catch (\Exception $e) {
                \Log::error("Erro ao enviar notificação para admins: " . $e->getMessage());
            }
            
            return redirect()->route('requisicoes.index')->with('success', 'Requisição criada com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar requisição: ' . $e->getMessage()])->withInput();
        }
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

        $bookRequest->load('items.livro', 'items.bookReview');

        return view('requisicoes.edit', compact('bookRequest', 'livros'));
    }

    public function update(Request $request, BookRequest $bookRequest)
    {
        \Log::info('Request POST data:', $request->all());
        $this->authorize('update', $bookRequest);

        $validator = Validator::make($request->all(), [
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date',
            'notas' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => ['nullable', 'integer', 'exists:book_request_items,id'],
            'items.*.livro_id' => ['required', 'integer', 'exists:livros,id'],
            'items.*.data_real_entrega' => ['nullable', 'date'],
            'items.*.dias_decorridos' => ['nullable', 'integer'],
            'items.*.status' => ['required', 'string'],
            'items.*.obs' => ['nullable', 'string'],
            'items.*.review_text' => ['nullable', 'string'],
        ]);

        if (!$validator->fails()) {
            $dataInicio = Carbon::parse($request->input('data_inicio'));

            foreach ($request->input('items', []) as $index => $item) {
                if (!empty($item['data_real_entrega'])) {
                    $dataEntrega = Carbon::parse($item['data_real_entrega']);
                    if ($dataEntrega->lt($dataInicio)) {
                        $validator->errors()->add("items.$index.data_real_entrega", "Data de Entrega Real deve ser maior ou igual à data de início.");
                    }
                }
            }
        }

        if ($validator->fails()) {
            \Log::error('Falha na validação em update de requisição', [
                'errors' => $validator->errors()->all(),
                'input' => $request->all(),
            ]);
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        if ($bookRequest->data_fim != $validated['data_fim']) {
            $bookRequest->lembrete_enviado_em = null;
            $bookRequest->lembrete_enviado_para = null;
        }

        $isAtivo = false;

        \DB::beginTransaction();

        try {
            \Log::info('Iniciando atualização dos itens.');
            foreach ($validated['items'] as $itemData) {
                $isAtivo = $isAtivo || in_array($itemData['status'], ['realizada', 'nao_entregue']);

                if (!empty($itemData['id'])) {
                    $bookRequestItem = BookRequestItem::find($itemData['id']);
                    if ($bookRequestItem) {

                        $bookRequestItem->update([
                            'livro_id' => $itemData['livro_id'],
                            'data_real_entrega' => $itemData['data_real_entrega'] ?? null,
                            'dias_decorridos' => $itemData['dias_decorridos'] ?? null,
                            'status' => $itemData['status'],
                            'obs' => $itemData['obs'] ?? null,
                        ]);

                        $livro = Livro::find($itemData['livro_id']);
                        $livro->status = in_array($itemData['status'], ['realizada', 'nao_entregue']) ? $livro->status : 'disponivel';
                        $livro->save();

                        $user = auth()->user();
                        if ($user->isCidadao() && in_array($itemData['status'], ['entregue_ok', 'entregue_obs'])) {
                            $reviewText = trim($itemData['review_text'] ?? '');

                            if ($reviewText !== '') {
                                $existingReview = BookReview::where('book_request_item_id', $bookRequestItem->id)
                                    ->where('user_id', $user->id)
                                    ->first();

                                if (!$existingReview) {
                                    $review = BookReview::create([
                                        'book_request_item_id' => $bookRequestItem->id,
                                        'livro_id' => $bookRequestItem->livro_id,
                                        'user_id' => $user->id,
                                        'review_text' => $reviewText,
                                        'status' => 'suspenso',
                                        'admin_justification' => null,
                                    ]);

                                    $admins = User::where('role', 'admin')->pluck('email')->toArray();
                                    Mail::to($admins)->send(new ReviewCreatedAdminNotification($review));
                                } else {
                                    if (in_array($existingReview->status, ['ativo', 'recusado'])) {
                                        $existingReview->status = 'suspenso';
                                        $existingReview->admin_justification = null;
                                    }
                                    $existingReview->review_text = $reviewText;
                                    $existingReview->save();

                                    if (in_array($existingReview->status, ['suspenso'])) {
                                        $admins = User::where('role', 'admin')->pluck('email')->toArray();
                                        Mail::to($admins)->send(new ReviewCreatedAdminNotification($existingReview));
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $newItem = $bookRequest->items()->create([
                        'livro_id' => $itemData['livro_id'],
                        'data_real_entrega' => $itemData['data_real_entrega'] ?? null,
                        'dias_decorridos' => $itemData['dias_decorridos'] ?? null,
                        'status' => $itemData['status'],
                        'obs' => $itemData['obs'] ?? null,
                    ]);

                    $livro = Livro::find($itemData['livro_id']);
                    $livro->status = in_array($itemData['status'], ['realizada', 'nao_entregue']) ? $livro->status : 'disponivel';
                    $livro->save();
                }
            }

            $validated['ativo'] = $isAtivo;
            \Log::info('Itens atualizados, atualizando pedido.');
            // Atualiza dados do pedido
            $bookRequest->update([
                'data_inicio' => $validated['data_inicio'],
                'data_fim' => $validated['data_fim'],
                'notas' => $validated['notas'],
                'ativo' => $validated['ativo'],
                'lembrete_enviado_em' => $bookRequest->lembrete_enviado_em,
                'lembrete_enviado_para' => $bookRequest->lembrete_enviado_para,
            ]);
            \Log::info('Pedido atualizado, commit.');
            \DB::commit();
            \Log::info('Transação commitada com sucesso.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erro inesperado durante atualização de requisição: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'Erro inesperado ao atualizar a requisição.'])->withInput();
        }

        return redirect()->route('requisicoes.show', $bookRequest)->with('success', 'Requisição atualizada com sucesso!');
    }



    public function searchUsers(Request $request)
    {
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
                ->orWhere('email', 'like', "%{$query}%"); // incluído email aqui
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
