<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Editora;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Exports\EditorasExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class EditoraController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = $request->input('query');

        $editoras = Editora::when($query, function ($q) use ($query) {
            $q->where('nome', 'like', "%{$query}%");
        })
        ->paginate(12)
        ->appends($request->only('query'));  

        return view('editoras.index', compact('editoras', 'query'));
    }

    public function show(Editora $editora)
    {
        return view('editoras.show', compact('editora'));
    }

    public function check(Request $request)
    {
        $nome = $request->query('nome');
        $editora = Editora::where('nome', $nome)->first();
        return response()->json([
            'id' => $editora ? $editora->id : null,
            'nome' => $editora ? $editora->nome : null,
        ]);
    }

    // public function editJson($id)
    // {
    //     $editora = Editora::findOrFail($id);

    //     return response()->json([
    //         'nome' => $editora->nome,
    //         'logo_url' => $editora->logo_url,
    //     ]);
    // }

    public function edit(Editora $editora)
    {
        return view('editoras.edit', compact('editora'));
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'editoras_' . now()->format('Ymd_His') . '.xlsx';

        return (new EditorasExport($request->query('query')))->download($fileName);
    }

    public function exportPdf(Request $request)
    {
        $editoras = Editora::when($request->query('query'), fn($q) => $q->where('nome', 'like', "%{$request->query('query')}%"))->get();

        $pdf = PDF::loadView('editoras.export_pdf', compact('editoras'));

        return $pdf->download('editoras_' . now()->format('Ymd_His') . '.pdf');
    }

    public function update(Request $request, Editora $editora)
    {
        $this->authorize('update', $editora);
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($editora->logo_url) {
                \Storage::disk('public')->delete($editora->logo_url);
            }
            $validated['logo_url'] = $request->file('logo')->store('editoras', 'public');
        }

        $editora->update($validated);

        return redirect()->route('editoras.index')->with('success', 'Editora atualizada com sucesso.');
    }

    public function destroy(Editora $editora)
    {
        $this->authorize('delete', $editora);
        if ($editora->livros()->count() > 0) {
            return redirect()->back()
                ->with('warning', 'Esta editora está associada a livros. Para continuar esse processo remova os livros associados a essa editora primeiro.');
        }

        
        if ($editora->foto_url) {
            \Storage::disk('public')->delete($editora->foto_url);
        }

        $editora->delete();

        return redirect()->route('editoras.index')->with('success', 'Editora excluída com sucesso.');
    }

    public function create()
    {
        $this->authorize('create', Editora::class);
        return view('editoras.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Editora::class);
        $validated = $request->validate([
            'nome' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_url'] = $request->file('logo')->store('editoras', 'public');
        }

        Editora::create($validated);

        return redirect()->route('editoras.index')->with('success', 'Editora criada com sucesso.');
    }
}

