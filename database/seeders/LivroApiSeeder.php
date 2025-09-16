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
            'maxResults' => 10, // ajuste o número que desejar
        ]);

        if ($response->successful()) {
            $items = $response->json('items', []);

            foreach ($items as $index => $item) {
                $volumeInfo = $item['volumeInfo'];

                // Cria ou obtém editora com foto faker para novos
                $editora = null;
                if (!empty($volumeInfo['publisher'])) {
                    $editora = Editora::firstOrCreate(
                        ['nome' => $volumeInfo['publisher']],
                        ['logo_url' => 'https://picsum.photos/150/200?image=' . rand(1, 1000)]
                    );
                }

                // Extrai o ISBN ou gera um falso único se não existir
                $isbn = $this->extractIsbn($volumeInfo['industryIdentifiers'] ?? [], $index);

                // Cria o livro
                $livro = Livro::create([
                    'titulo' => $volumeInfo['title'] ?? 'Sem título',
                    'isbn' => $isbn,
                    'bibliografia' => $volumeInfo['description'] ?? null,
                    'capa_url' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
                    'preco' => rand(10, 200),
                    'status' => 'disponivel',
                    'editora_id' => $editora?->id,
                ]);

                // Cria autores novos ou existentes com foto faker
                if (!empty($volumeInfo['authors'])) {
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
    }


    /**
     * Extrai ISBN ou gera um falso único se não encontrado
     * @param array $identifiers Array da API com tipos e identificadores
     * @param int $index índice do loop para ajudar a garantir unicidade
     * @return string ISBN real ou fake único
     */
    private function extractIsbn(array $identifiers, int $index): string
    {
        foreach ($identifiers as $id) {
            if (str_contains($id['type'], 'ISBN')) {
                return $id['identifier'];
            }
        }
        // Gera um ISBN fake único para não causar conflito no banco
        return 'fake-isbn-' . $index . '-' . uniqid();
    }
}
