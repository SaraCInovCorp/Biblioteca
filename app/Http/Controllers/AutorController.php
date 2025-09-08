<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Autor;
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
use App\Exports\AutoresExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AutorController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = $request->input('query');

        $autores = Autor::when($query, function ($q) use ($query) {
            $q->where('nome', 'like', "%{$query}%");
        })
        ->paginate(12)
        ->appends($request->only('query'));

        return view('autores.index', compact('autores', 'query'));
    }

    public function show(Autor $autor)
    {
        return view('autores.show', compact('autor'));
    }

    public function edit(Autor $autor)
    {
        $this->authorize('update', $autor);
        return view('autores.edit', compact('autor'));
    }

    // public function editJson($id)
    // {
    //     $autor = Autor::findOrFail($id);

    //     return response()->json([
    //         'nome' => $autor->nome,
    //         'foto_url' => $autor->foto_url,

    //     ]);
    // }

    public function exportExcel(Request $request)
    {
        $fileName = 'autores_' . now()->format('Ymd_His') . '.xlsx';

        return (new AutoresExport($request->query('query')))->download($fileName);
    }

    public function exportPdf(Request $request)
    {
        $autores = Autor::when($request->query('query'), fn($q) => $q->where('nome', 'like', "%{$request->query('query')}%"))
            ->get();

        $pdf = PDF::loadView('autores.export_pdf', compact('autores'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('autores_' . now()->format('Ymd_His') . '.pdf');
    }

    public function update(Request $request, Autor $autor)
    {
        $this->authorize('update', $autor);
        $validated = $request->validate([
            'nome' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($autor->foto_url) {
                \Storage::disk('public')->delete($autor->foto_url);
            }
            $validated['foto_url'] = $request->file('foto')->store('autores', 'public');
        }

        $autor->update($validated);

        return redirect()->route('autores.index')->with('success', 'Autor atualizado com sucesso.');
    }

    public function destroy(Autor $autor)
    {
        $this->authorize('delete', $autor);
        if ($autor->foto_url) {
            \Storage::disk('public')->delete($autor->foto_url);
        }
        $autor->delete();

        return redirect()->route('autores.index')->with('success', 'Autor excluído com sucesso.');
    }

    public function create()
    {
        $this->authorize('create', Autor::class);
        return view('autores.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Autor::class);
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto_url'] = $request->file('foto')->store('autores', 'public');
        }

        Autor::create($validated);

        return redirect()->route('autores.index')->with('success', 'Autor criado com sucesso.');
    }
}
