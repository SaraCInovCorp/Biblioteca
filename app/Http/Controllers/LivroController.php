<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Exports\LivrosExport;
use App\Models\BookRequestItem;
use App\Models\BookRequest;
use App\Models\LivroWaitingList;
use Maatwebsite\Excel\Facades\Excel;
use PDF; // Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\LivroDisponivelNotification;
use Illuminate\Support\Facades\Notification;

class LivroController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = $request->input('query');        
        $editoraId = $request->input('editora'); 
        $autorId = $request->input('autor');      

        $livros = Livro::with(['editora', 'autores'])
            ->when($query, function ($q) use ($query) {
                $q->where('titulo', 'like', "%{$query}%");
            })
            ->when($editoraId, function ($q) use ($editoraId) {
                $q->where('editora_id', $editoraId);
            })
            ->when($autorId, function ($q) use ($autorId) {
                $q->whereHas('autores', function ($q2) use ($autorId) {
                    $q2->where('autores.id', $autorId);
                });
            })
            ->paginate(6)
            ->appends($request->only(['query', 'editora', 'autor'])); 

        $editoras = Editora::all();
        $autores = Autor::all();

        return view('livros.index', compact('livros', 'editoras', 'autores'));
    }

    public function exportExcel(Request $request)
    {
        $ids = $request->query('ids', []);
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?: explode(',', $ids);
        }
        $ids = (array) $ids;

        $fileName = 'livros_' . now()->format('Ymd_His') . '.xlsx';

        if (!empty($ids)) {
            return (new LivrosExport(null, null, null, $ids))->download($fileName);
        }

        return (new LivrosExport(
            $request->query('query'),
            $request->query('editora'),
            $request->query('autor'),
        ))->download($fileName);
    }

    public function exportPdf(Request $request)
    {
        $ids = $request->query('ids', []);
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?: explode(',', $ids);
        }
        $ids = (array) $ids;

        if (!empty($ids)) {
            $livros = Livro::with(['editora', 'autores'])
                ->whereIn('id', $ids)
                ->get();
        } else {
            $livros = Livro::query()
                ->when($request->query('query'), fn($q) => $q->where('titulo', 'like', "%".$request->query('query')."%"))
                ->when($request->query('editora'), fn($q) => $q->where('editora_id', $request->query('editora')))
                ->when($request->query('autor'), fn($q) => $q->whereHas('autores', fn($q2) => $q2->where('id', $request->query('autor'))))
                ->get();
        }

        $pdf = PDF::loadView('livros.export_pdf', compact('livros'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('livros_' . now()->format('Ymd_His') . '.pdf');
    }


    public function create()
    {
        $this->authorize('create', Livro::class);
        $editoras = Editora::all();
        $autores = Autor::all();
        
        return view('livros.create', compact('editoras', 'autores'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Livro::class);
        $validated = $request->validate([
            'titulo' => 'required|string',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'capa_url' => 'nullable|string',
            'capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'isbn' => 'required|digits_between:10,13|unique:livros,isbn',
            'nova_editora' => 'nullable|string',
            'editora_id' => 'required_without:nova_editora',
            'novos_autores' => 'nullable|array',
            'novos_autores.*' => 'nullable|string',
            'autores' => 'required_without:novos_autores|array|min:1',
            'autores.*' => 'nullable|string',
            
        ]);

        if ($request->filled('nova_editora')) {
            $editora = Editora::firstOrCreate(
                ['nome' => $request->nova_editora],
                [
                    'origem' => 'manual',          
                    'user_id' => auth()->id(),
                ]
            );
            $editoraId = $editora->id;
        } else {
            $editoraId = $validated['editora_id'];
        }

        $autor_ids = $validated['autores'] ?? [];

        if ($request->filled('novos_autores')) {
            foreach ($request->novos_autores as $novoAutor) {
                if ($novoAutor) {
                    $autor = Autor::firstOrCreate(
                        ['nome' => $novoAutor],
                        [
                            'origem' => 'manual', 
                            'user_id' => auth()->id(),
                        ]
                    );
                    $autor_ids[] = $autor->id;
                }
            }
        }

        $autor_ids = array_filter($autor_ids, fn($id) => !empty($id));

        if ($request->hasFile('capa')) {
            $caminhoCapa = $request->file('capa')->store('capas', 'public');
        } elseif ($request->filled('capa_url')) {
            $caminhoCapa = $request->input('capa_url');
        } else {
            $caminhoCapa = null;
        }
        //Log::info('Caminho capa: ' . $caminhoCapa);

        $dadosLivro = [
            'titulo' => $validated['titulo'],
            'bibliografia' => $validated['bibliografia'] ?? null,
            'preco' => $validated['preco'],
            'capa_url' => $caminhoCapa,
            'isbn' => $validated['isbn'],
            'status' => 'disponivel',
            'editora_id' => $editoraId,
            'origem' => 'manual',   // ou 'import' se livro vier de importação
            'user_id' => auth()->id(),
        ];

        $livro = Livro::create($dadosLivro);

        $livro->autores()->sync($autor_ids);
        //Log::info($livro);

        return redirect()->route('livros.index')->with('success', 'Livro criado com sucesso!');
    }

    public function show(Livro $livro)
    {
        $livro->load(['autores', 'editora']);

        function normalizeText($text) {
            $text = mb_strtolower($text);
            $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
            $text = preg_replace('/\b(da|de|do|e|a|o|que|em|no|na|por|para|com|um|uma|uns|umas)\b/u', '', $text);
            $text = preg_replace('/\s+/', ' ', $text);
            return trim($text);
        }

        $livroDesc = normalizeText($livro->bibliografia ?? '');

        $outrosLivros = Livro::where('id', '!=', $livro->id)
            ->whereNotNull('bibliografia')
            ->where('bibliografia', '!=', '')
            ->get();

        $livrosRelacionados = $outrosLivros->map(function ($item) use ($livroDesc) {
            $itemDesc = normalizeText($item->bibliografia ?? '');

            similar_text($livroDesc, $itemDesc, $similaridade);

            $palavrasLivro = explode(' ', $livroDesc);
            $palavrasItem = explode(' ', $itemDesc);

            $palavrasComuns = array_intersect($palavrasLivro, $palavrasItem);
            $percentualPalavrasComuns = count($palavrasComuns) / max(count($palavrasLivro), 1) * 100;

            $item->similaridade = 0.7 * $similaridade + 0.3 * $percentualPalavrasComuns;

            return $item;
        })->sortByDesc('similaridade')->take(2);

        $user = Auth::user();

        $totalRequisicoes = $livro->bookRequestItems()->count();

        $totalUsuarios = DB::table('book_request_items')
            ->join('book_requests', 'book_request_items.book_request_id', '=', 'book_requests.id')
            ->where('book_request_items.livro_id', $livro->id)
            ->distinct()
            ->count('book_requests.user_id');

        if (!$user) {
            $historico = collect();
            $estaInscrito = false;
        } elseif ($user->isAdmin()) {
            $historico = $livro->bookRequestItems()
                ->with('bookRequest.user')
                ->orderByDesc('created_at')
                ->get();
            $estaInscrito = false;
        } else {
            $historico = $livro->bookRequestItems()
                ->whereHas('bookRequest', fn($q) => $q->where('user_id', $user->id))
                ->with('bookRequest')
                ->orderByDesc('created_at')
                ->get();
            
            $estaInscrito = $livro->waitingList()
                ->where('user_id', $user->id)
                ->where('ativo', true)
                ->exists();
        }

        $allReviews = $livro->reviews()->with('user')->get();
        $approvedReviews = $allReviews->where('status', 'ativo');

        return view('livros.show', compact('livro', 'historico', 'totalRequisicoes', 'totalUsuarios', 'approvedReviews', 'estaInscrito', 'livrosRelacionados'));
    }

    public function buscarMaisRelacionados(Request $request, Livro $livro)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 2;

        function normalizeText($text) {
            $text = mb_strtolower($text);
            $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
            $text = preg_replace('/\b(da|de|do|e|a|o|que|em|no|na|por|para|com|um|uma|uns|umas)\b/u', '', $text);
            $text = preg_replace('/\s+/', ' ', $text);
            return trim($text);
        }

        $livroDesc = normalizeText($livro->bibliografia ?? '');

        $outrosLivros = Livro::where('id', '!=', $livro->id)
            ->whereNotNull('bibliografia')
            ->where('bibliografia', '!=', '')
            ->get();

        $livrosRelacionados = $outrosLivros->map(function ($item) use ($livroDesc) {
            $itemDesc = normalizeText($item->bibliografia ?? '');

            similar_text($livroDesc, $itemDesc, $similaridade);

            $palavrasLivro = explode(' ', $livroDesc);
            $palavrasItem = explode(' ', $itemDesc);

            $palavrasComuns = array_intersect($palavrasLivro, $palavrasItem);
            $percentualPalavrasComuns = count($palavrasComuns) / max(count($palavrasLivro), 1) * 100;

            $item->similaridade = 0.7 * $similaridade + 0.3 * $percentualPalavrasComuns;

            return $item;
        })->sortByDesc('similaridade');

        // Paginação manual
        $paginated = $livrosRelacionados->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'livros' => $paginated,
            'nextPage' => $page + 1,
            'hasMore' => $livrosRelacionados->count() > $page * $perPage,
        ]);
    }


    public function pesquisarGoogleBooks(Request $request)
    {
        $query = $request->query('q');
        if (!$query) {
            return response()->json(['error' => 'Parâmetro de busca obrigatório'], 400);
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => 10,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Erro ao buscar no Google Books'], 500);
        }

        return $response->json();
    }

    public function edit($id)
    {
        $livro = Livro::with('autores')->findOrFail($id);
        $this->authorize('update', $livro);
        $editoras = Editora::all();
        $autores = Autor::all();
        $selectedAutores = $livro->autores->pluck('id')->toArray();

        return view('livros.edit', compact('livro', 'editoras', 'autores', 'selectedAutores'));
    }

    public function update(Request $request, Livro $livro)
    {
        $this->authorize('update', $livro);
        $validated = $request->validate([
            'titulo' => 'required|string',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'capa_url' => 'nullable|string',
            'isbn' => 'required|digits_between:10,13|unique:livros,isbn,' . $livro->id,
            'status' => 'required|in:disponivel,indisponivel,requisitado', 
            'editora_id' => 'required|exists:editoras,id',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autores,id',
            'novos_autores' => 'nullable|array',
            'novos_autores.*' => 'nullable|string',
        ]);

        if ($request->hasFile('capa')) {
            $validated['capa_url'] = $request->file('capa')->store('capas', 'public');
        } elseif ($request->filled('capa_url')) {
            $validated['capa_url'] = $request->input('capa_url');
        }

        $autor_ids = $validated['autores'] ?? [];

        if ($request->filled('novos_autores')) {
            foreach ($request->novos_autores as $novoAutor) {
                if ($novoAutor) {
                    $autor = Autor::firstOrCreate(['nome' => $novoAutor]);
                    $autor_ids[] = $autor->id;
                }
            }
        }
        $autor_ids = array_filter($autor_ids, fn($id) => !empty($id));

        $livro->update($validated);

        $livro->autores()->sync($validated['autores']);

        return redirect()->route('livros.index')->with('success', 'Livro atualizado com sucesso!');
    }

    public function destroy(Livro $livro)
    {
        $livro->status = $livro->status === 'disponivel' ? 'indisponivel' : 'disponivel';
        $livro->save();

        if ($livro->status === 'disponivel') {
            // Buscar usuários ativos na lista de espera desse livro
            $inscritos = LivroWaitingList::with('user')
                ->where('livro_id', $livro->id)
                ->where('ativo', true)
                ->get();

            $usuarios = $inscritos->pluck('user')->filter();

            // Enviar notificação para todos os usuários
            Notification::send($usuarios, new LivroDisponivelNotification($livro));

            // Marcar as inscrições como notificadas e inativas
            foreach ($inscritos as $inscricao) {
                $inscricao->ativo = false;
                $inscricao->notificado_em = now();
                $inscricao->save();
            }
        }

        return back()->with('success', 'Status do livro atualizado e notificações enviadas!');
    }

}
