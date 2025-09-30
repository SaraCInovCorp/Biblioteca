<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\Livro;

class EncomendaItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Encomenda::all()->each(function ($encomenda) {
            $livros = Livro::inRandomOrder()->take(rand(1,5))->get();
            foreach ($livros as $livro) {
                EncomendaItem::factory()->create([
                    'encomenda_id' => $encomenda->id,
                    'livro_id' => $livro->id,
                    'preco_unitario' => $livro->preco,
                ]);
            }
        });
    }
}
