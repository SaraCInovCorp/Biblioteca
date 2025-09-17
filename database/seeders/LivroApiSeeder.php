<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;

class LivroApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lê o termo de busca para a API no .env, ou usa 'programming' como padrão
        $q = env('BOOK_API_QUERY', 'programming');

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $q,
            'maxResults' => 40, 
        ]);

        if ($response->successful()) {
            $items = $response->json('items', []);

            foreach ($items as $index => $item) {
                $volumeInfo = $item['volumeInfo'];

                if (
                    empty($volumeInfo['publisher']) ||
                    empty($volumeInfo['authors']) ||
                    empty($volumeInfo['industryIdentifiers']) ||
                    !$this->hasValidIsbn($volumeInfo['industryIdentifiers'])
                ) {
                    continue; 
                }

                $editora = Editora::firstOrCreate(
                    ['nome' => $volumeInfo['publisher']],
                    ['logo_url' => 'https://picsum.photos/150/200?image=' . rand(1, 1000)]
                );

                $isbn = $this->extractIsbn($volumeInfo['industryIdentifiers'] ?? [], $index);
                $preco = $item['saleInfo']['listPrice']['amount'] ?? rand(10, 200);
                $livro = Livro::create([
                    'titulo' => $volumeInfo['title'] ?? 'Sem título',
                    'isbn' => $isbn,
                    'bibliografia' => $volumeInfo['description'] ?? null,
                    'capa_url' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
                    'preco' => $preco,
                    'status' => 'disponivel',
                    'editora_id' => $editora?->id,
                ]);

                $autor_ids = [];
                foreach ($volumeInfo['authors'] as $nomeAutor) {
                    $autor = Autor::firstOrCreate(
                        ['nome' => $nomeAutor],
                        ['foto_url' => 'https://picsum.photos/150/200?image=' . rand(1, 1000)]
                    );
                    $autor_ids[] = $autor->id;
                }
                $livro->autores()->sync($autor_ids);
            }
        }
    }

    /**
     * Checa se existe ISBN válido no array dos identificadores.
     */
    private function hasValidIsbn(array $identifiers): bool
    {
        foreach ($identifiers as $id) {
            if (str_contains($id['type'], 'ISBN') && !empty($id['identifier'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extrai ISBN ou cria um falso único.
     */
    private function extractIsbn(array $identifiers, int $index): string
    {
        foreach ($identifiers as $id) {
            if (str_contains($id['type'], 'ISBN')) {
                return $id['identifier'];
            }
        }
        return 'fake-isbn-' . $index . '-' . uniqid();
    }
}
