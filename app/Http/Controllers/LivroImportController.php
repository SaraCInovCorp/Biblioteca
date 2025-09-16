<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Support\Facades\Http;

class LivroImportController extends Controller
{
    /**
     * Exibe a página de importação.
     */
    public function showImportPage()
    {
        $this->authorize('create', Livro::class);

        return view('livros.import');
    }

    /**
     * Busca livros na API Google Books conforme o termo.
     */
    public function searchGoogleBooks(Request $request)
    {
        $this->authorize('create', Livro::class);

        $query = $request->query('q');
        if (!$query) {
            return response()->json(['error' => 'Parâmetro de busca obrigatório'], 400);
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => 20,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Erro ao consultar Google Books'], 500);
        }

        return $response->json();
    }

    /**
     * Importa os livros selecionados.
     */
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

            // Validação: ISBN presente e não vazio
            $isbn = $item['isbn'] ?? null;
            if (empty($isbn)) {
                $naoImportados[] = [
                    'titulo' => $item['title'] ?? 'Título desconhecido',
                    'motivo' => 'ISBN ausente ou inválido',
                ];
                continue;
            }

            // Evitar duplicidade
            if (Livro::where('isbn', $isbn)->exists()) {
                $naoImportados[] = [
                    'titulo' => $item['title'] ?? 'Título desconhecido',
                    'motivo' => 'Livro com ISBN já cadastrado',
                ];
                continue;
            }

            // Criar ou pegar editora
            $editora = null;
            if (!empty($item['publisher'])) {
                $editora = Editora::firstOrCreate(['nome' => $item['publisher']]);
            }

            // Criar livro
            $livro = Livro::create([
                'isbn' => $isbn,
                'titulo' => $item['title'] ?? 'Sem título',
                'bibliografia' => $item['description'] ?? null,
                'preco' => rand(10, 200),
                'capa_url' => $item['thumbnail'] ?? null,
                'status' => 'disponivel',
                'editora_id' => $editora?->id,
            ]);

            // Criar autores e ligar ao livro
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
