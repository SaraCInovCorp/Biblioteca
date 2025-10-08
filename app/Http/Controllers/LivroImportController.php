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
use Illuminate\Support\Facades\Auth;
use App\Exports\LivrosExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class LivroImportController extends Controller
{
    public function exportPdfPorImportacao(Request $request, $importacaoId)
    {
        $importacao = Importacao::with('livros')->findOrFail($importacaoId);
        $livros = $importacao->livros;

        $pdf = PDF::loadView('livros.export_pdf', ['livros' => $livros]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($importacao)
            ->event('exportpdfimportacao')
            ->useLog('exportpdfimportacao-livro')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'livros_ids' => $livros->pluck('id')->toArray(),
            ])
            ->log('Exportação PDF por importação');

        return $pdf->download('importacao_'.$importacaoId.'_livros.pdf');
    }

    public function exportExcelPorImportacao(Request $request, $importacaoId)
    {
        $importacao = Importacao::with('livros')->findOrFail($importacaoId);
        $livroIds = $importacao->livros->pluck('id')->toArray();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($importacao)
            ->event('exportexcelfimportacao')
            ->useLog('exportexcelimportacao-livro')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'livros_ids' => $livroIds,
            ])
            ->log('Exportação Excel por importação');

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

        $startIndex = max(0, (int) $request->query('startIndex', 0)); // Garante >=0
        $maxResults = 40;

        // Garante que não vai ultrapassar 1000 resultados (limite do Google)
        if ($startIndex + $maxResults > 1000) {
            return response()->json(['error' => 'Limite máximo de resultados/índice atingido'], 422);
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => $maxResults,
            'orderBy' => 'relevance',
            'langRestrict' => 'pt',
            'printType' => 'books',
            'startIndex' => $startIndex,
        ]);

        if ($response->failed()) {
            // Log completo para debug
            \Log::error('Erro Google Books:', [
                'url' => $response->effectiveUri(),
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['error' => 'Erro ao consultar Google Books', 'mensagem' => $response->body()], 500);
        }

        activity()
            ->causedBy(auth()->user())
            ->event('searchgooglebooks')
            ->useLog('searchgooglebooks-livro')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'query' => $query,
                'startIndex' => $startIndex,
                'status' => $response->status(),
            ])
            ->log('Busca Google Books API');

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

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($livro)
                    ->event('importselected')
                    ->useLog('importselected-livro')
                    ->withProperties([
                        'ip' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'importacao_id' => $importacao->id,
                    ])
                    ->log('Livro importado via API Google Books');

                Log::info('Livro importado: ', ['livro_id' => $livro->id]);
            }
            DB::commit();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($importacao)
                ->event('importselected')
                ->useLog('importselected-livro')
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'livros_importados' => $importadosIds,
                    'livros_nao_importados' => $naoImportados,
                ])
                ->log('Importação de livros concluída');

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

        activity()
            ->causedBy(Auth::user())
            ->performedOn($importacao)
            ->event('importacaodetalhes')
            ->useLog('importacaodetalhes-livro')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ])
            ->log('Visualização da importação detalhada');

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

        activity()
            ->causedBy(Auth::user())
            ->event('listaimportados')
            ->useLog('listaimportados-livro')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ])
            ->log('Visualização da lista de importações');
        
        return view('livros.importados-lista', compact('importacoes', 'ultimaImportacao', 'livros'));
    }



}