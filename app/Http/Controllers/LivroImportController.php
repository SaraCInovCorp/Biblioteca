<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Support\Facades\Http;

class LivroImportController extends Controller
{
    public function showImportPage()
    {
        $this->authorize('create', Livro::class);

        return view('livros.import');
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

        $livros = $request->input('livros', []);
        if (empty($livros)) {
            return redirect()->back()->withErrors('Nenhum livro selecionado para importação.');
        }

        $naoImportados = [];

        foreach ($livros as $itemJson) {
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
                $editora = Editora::firstOrCreate(['nome' => $item['publisher']]);
            }

            $livro = Livro::create([
                'isbn' => $isbn,
                'titulo' => $item['title'] ?? 'Sem título',
                'bibliografia' => $item['description'] ?? null,
                'preco' => $item['preco'] ?? rand(10, 200),
                'capa_url' => $item['thumbnail'] ?? null,
                'status' => 'disponivel',
                'editora_id' => $editora?->id,
            ]);

            $autorIds = [];
            foreach ($item['authors'] ?? [] as $nomeAutor) {
                $autor = Autor::firstOrCreate(['nome' => $nomeAutor]);
                $autorIds[] = $autor->id;
            }
            $livro->autores()->sync($autorIds);
        }

        if (!empty($naoImportados)) {
            return redirect()->back()->with('warning_import', $naoImportados);
        }

        return redirect()->back()->with('success', 'Livros importados com sucesso!');
    }
}