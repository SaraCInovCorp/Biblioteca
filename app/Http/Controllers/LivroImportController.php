<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Importacao;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Exports\LivrosExport;
use Maatwebsite\Excel\Facades\Excel;

class LivroImportController extends Controller
{
    public function exportPdfPorImportacao($importacaoId)
    {
        $importacao = Importacao::with('livros')->findOrFail($importacaoId);
        $livros = $importacao->livros;

        $pdf = PDF::loadView('livros.export_pdf', ['livros' => $livros]);

        return $pdf->download('importacao_'.$importacaoId.'_livros.pdf');
    }

    public function exportExcelPorImportacao($importacaoId)
    {
        $importacao = Importacao::with('livros')->findOrFail($importacaoId);
        $livroIds = $importacao->livros->pluck('id')->toArray();

        return Excel::download(new LivrosExport(null, null, null, $livroIds), 'importacao_'.$importacaoId.'_livros.xlsx');
    }



    public function showImportPage()
    {
        $this->authorize('create', Livro::class);

        $importacoes = Importacao::with(['livros.editora', 'livros.autores', 'editoras', 'autores'])
            ->where('user_id', auth()->id())
            ->orderByDesc('imported_at')
            ->get();

        $ultimaImportacao = $importacoes->first();

        $livros = $ultimaImportacao ? $ultimaImportacao->livros : collect();

        return view('livros.import', compact('importacoes', 'livros', 'ultimaImportacao'));
    }

    public function searchGoogleBooks(Request $request)
    {
        $this->authorize('create', Livro::class);

       $query = $request->query('q');
        if (!$query) {
            return response()->json(['error' => 'Parâmetro de busca obrigatório'], 400);
        }

        $startIndex = (int) $request->query('startIndex', 0);

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => 40,
            'orderBy' => 'relevance',    
            'langRestrict' => 'pt', 
            'printType' => 'books', 
            'startIndex' => $startIndex,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Erro ao consultar Google Books'], 500);
        }

        return $response->json();
    }

    public function importSelected(Request $request)
    {
        $this->authorize('create', Livro::class);

        $livrosInput = $request->input('livros', []);
        if (empty($livrosInput)) {
            return $this->showImportPage()->withErrors('Nenhum livro selecionado para importação.');
        }

        $naoImportados = [];
        $importadosIds = [];

        DB::beginTransaction();
        try {
            $importacao = Importacao::create([
                'user_id' => auth()->id(),
                'api' => 'google_books',
                'imported_at' => now(),
            ]);

            foreach ($livrosInput  as $itemJson) {
                $item = json_decode($itemJson, true);

                $isbn = $item['isbn'] ?? null;
                if (empty($isbn)) {
                    $naoImportados[] = [
                        'titulo' => $item['title'] ?? 'Título desconhecido',
                        'motivo' => 'ISBN ausente ou inválido',
                    ];
                    continue;
                }

                if (Livro::where('isbn', $isbn)->exists()) {
                    $naoImportados[] = [
                        'titulo' => $item['title'] ?? 'Título desconhecido',
                        'motivo' => 'Livro com ISBN já cadastrado',
                    ];
                    continue;
                }

                $editora = null;
                if (!empty($item['publisher'])) {
                    $editora = Editora::firstOrCreate(
                        ['nome' => $item['publisher']],
                        ['origem' => 'import_google_books', 'user_id' => auth()->id()]
                    );
                }

                $livro = Livro::create([
                    'isbn' => $isbn,
                    'titulo' => $item['title'] ?? 'Sem título',
                    'bibliografia' => $item['description'] ?? null,
                    'preco' => $item['preco'] ?? rand(10, 200),
                    'capa_url' => $item['thumbnail'] ?? null,
                    'status' => 'disponivel',
                    'editora_id' => $editora?->id,
                    'origem' => 'import_google_books',
                    'user_id' => auth()->id(),
                ]);

                $autorIds = [];
                foreach ($item['authors'] ?? [] as $nomeAutor) {
                    $autor = Autor::firstOrCreate(
                        ['nome' => $nomeAutor],
                        ['origem' => 'import_google_books', 'user_id' => auth()->id()]
                    );
                    $autorIds[] = $autor->id;
                }
                $livro->autores()->sync($autorIds);

                $importacao->livros()->attach($livro->id);
                if ($editora) {
                    $importacao->editoras()->syncWithoutDetaching($editora->id);
                }
                $importacao->autores()->syncWithoutDetaching($autorIds);

                $importadosIds[] = $livro->id;
                Log::info('Livro importado: ', ['livro_id' => $livro->id]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->showImportPage()->withErrors('Erro ao importar livros: ' . $e->getMessage());
        }

       $importacoes = Importacao::with(['livros.editora', 'livros.autores', 'editoras', 'autores'])
            ->where('user_id', auth()->id())
            ->orderByDesc('imported_at')
            ->get();

        $ultimaImportacao = $importacoes->first();

        $livros = $ultimaImportacao ? $ultimaImportacao->livros : collect();

        Log::info('Livros para exibição', ['count' => $livros->count(), 'ids' => $livros->pluck('id')]);
        Log::info('Livros não importados:', $naoImportados);

        if (!empty($naoImportados)) {
            return redirect()->route('livros.import.page')
                ->with('warning_import', $naoImportados);
        }

        return redirect()->route('livros.import.page')
            ->with('success', 'Todos os livros foram importados com sucesso!');
    }

    public function importacaoDetalhes(Importacao $importacao)
    {
        $this->authorize('view', $importacao);

        $importacoes = Importacao::with(['livros.editora', 'livros.autores', 'editoras', 'autores'])
            ->where('user_id', auth()->id())
            ->orderByDesc('imported_at')
            ->get();

        $livros = $importacao->livros;
        $ultimaImportacao = $importacao;

        return view('livros.import', compact('importacoes', 'livros', 'ultimaImportacao'));
    }

   public function listaImportados(Request $request)
    {
        $highlightImportacaoId = $request->query('highlight'); 

        $importacoes = Importacao::with(['livros.editora', 'livros.autores', 'editoras', 'autores'])
            ->where('user_id', auth()->id())
            ->orderByDesc('imported_at')
            ->paginate(10);

        if ($importacoes->isEmpty()) {
            return redirect()->route('livros.import.page')->withErrors('Nenhum livro importado encontrado.');
        }

        if ($highlightImportacaoId) {
            $ultimaImportacao = $importacoes->where('id', $highlightImportacaoId)->first();
        } else {
            $ultimaImportacao = $importacoes->first();
        }

        $livros = $ultimaImportacao ? $ultimaImportacao->livros : collect();

        return view('livros.importados-lista', compact('importacoes', 'ultimaImportacao', 'livros'));
    }



}