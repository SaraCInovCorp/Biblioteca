<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookRequest;
use App\Models\Livro;

class BookRequestSeeder extends Seeder
{
    public function run()
    {
        BookRequest::factory()->count(20)->create()->each(function ($bookRequest) {
            // Pega livros disponíveis aleatórios para essa requisição
            $livros = Livro::where('status', 'disponivel')->inRandomOrder()->take(rand(1, 3))->get();

            foreach ($livros as $livro) {
                // Cria o item usando o factory para garantir lógica e dados consistentes
                $bookRequest->items()->save(
                    \App\Models\BookRequestItem::factory()->make([
                        'livro_id' => $livro->id,
                        'book_request_id' => $bookRequest->id,
                    ])
                );
            }
        });
    }
}

