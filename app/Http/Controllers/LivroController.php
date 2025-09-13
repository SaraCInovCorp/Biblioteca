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
        $this->authorize('create', Livro::class);
        $validated = $request->validate([
            'titulo' => 'required|string',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'isbn' => 'required|digits_between:10,13|unique:livros,isbn',
            'status' => 'required|in:disponivel,indisponivel,requisitado', 
            'editora_id' => 'required|exists:editoras,id',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autores,id'
        ]);

        if ($request->hasFile('capa')) {
            $validated['capa'] = $request->file('capa')->store('capas', 'public');
        }

        $livro = Livro::create($validated);

        $livro->autores()->sync($validated['autores']);

        return redirect()->route('livros.index')->with('success', 'Livro criado com sucesso!');
    }

    public function show(Livro $livro)
    {
        $livro->load(['autores', 'editora', 'bookRequestItems.bookRequest.user']);

        $historico = $livro->bookRequestItems()->with('bookRequest.user')->orderByDesc('created_at')->get();

        return view('livros.show', compact('livro', 'historico'));
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
            'isbn' => 'required|digits_between:10,13|unique:livros,isbn,' . $livro->id,
            'status' => 'required|in:disponivel,indisponivel,requisitado', 
            'editora_id' => 'required|exists:editoras,id',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autores,id'
        ]);

        if ($request->hasFile('capa')) {
            $validated['capa'] = $request->file('capa')->store('capas', 'public');
        }

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
