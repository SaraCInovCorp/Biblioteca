<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Exports\LivrosExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF; // Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
            ->appends($request->only(['query', 'editora', 'autor'])); // mantém filtros na paginação

        $editoras = Editora::all();
        $autores = Autor::all();

        return view('livros.index', compact('livros', 'editoras', 'autores'));
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'livros_' . now()->format('Ymd_His') . '.xlsx';

        return (new LivrosExport(
            $request->query('query'),
            $request->query('editora'),
            $request->query('autor'),
        ))->download($fileName);
    }

    public function exportPdf(Request $request)
    {
        $livros = Livro::query()
            ->when($request->query('query'), fn($q) => $q->where('titulo', 'like', "%".$request->query('query')."%"))
            ->when($request->query('editora'), fn($q) => $q->where('editora_id', $request->query('editora')))
            ->when($request->query('autor'), fn($q) => $q->whereHas('autores', fn($q2) => $q2->where('id', $request->query('autor'))))
            ->get();

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
        //Log::info('Entrou no create');
        $this->authorize('create', Livro::class);
        //Log::info($request);
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
        //Log::info($validated);

        if ($request->filled('nova_editora')) {
            $editora = Editora::firstOrCreate(['nome' => $request->nova_editora]);
            $editoraId = $editora->id;
        } else {
            $editoraId = $validated['editora_id'];
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

        //Log::info($autor_ids);
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
        ];

        $livro = Livro::create($dadosLivro);

        $livro->autores()->sync($autor_ids);
        //Log::info($livro);

        return redirect()->route('livros.index')->with('success', 'Livro criado com sucesso!');
    }

    public function show(Livro $livro)
    {
        $livro->load(['autores', 'editora', 'bookRequestItems.bookRequest.user']);

        $historico = $livro->bookRequestItems()->with('bookRequest.user')->orderByDesc('created_at')->get();

        return view('livros.show', compact('livro', 'historico'));
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
        $this->authorize('delete', $livro);

        if ($livro->status === 'requisitado') {
            return redirect()->back()->withErrors(['error' => 'Não é possível alterar o status de um livro requisitado.']);
        }

        if ($livro->status === 'disponivel') {
            $livro->status = 'indisponivel';
        } else {
            $livro->status = 'disponivel';
        }

        $livro->save();

        return redirect()->back()->with('success', 'Status do livro atualizado com sucesso!');
    }

}
