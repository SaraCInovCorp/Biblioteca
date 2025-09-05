<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Exports\LivrosExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF; // Barryvdh\DomPDF\Facade\Pdf;

class LivroController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');        // texto para título
        $editoraId = $request->input('editora'); // filtro editora (id)
        $autorId = $request->input('autor');      // filtro autor (id)

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
        $editoras = Editora::all();
        $autores = Autor::all();
        
        return view('livros.create', compact('editoras', 'autores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'isbn' => 'required|digits_between:10,13|unique:livros,isbn',
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
        return view('livros.show', compact('livro'));
    }

    public function edit($id)
    {
        $livro = Livro::with('autores')->findOrFail($id);
        $editoras = Editora::all();
        $autores = Autor::all();
        $selectedAutores = $livro->autores->pluck('id')->toArray();

        return view('livros.edit', compact('livro', 'editoras', 'autores', 'selectedAutores'));
    }

    public function update(Request $request, Livro $livro)
    {
        $validated = $request->validate([
            'titulo' => 'required|string',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'isbn' => 'required|digits_between:10,13|unique:livros,isbn,' . $livro->id,
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
        $livro->delete();
        return redirect()->route('livros.index')->with('success', 'Livro excluído com sucesso!');
    }
}
