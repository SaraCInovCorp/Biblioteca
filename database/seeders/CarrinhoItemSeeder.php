<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carrinho;
use App\Models\CarrinhoItem;
use App\Models\Livro;


class CarrinhoItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Carrinho::all()->each(function ($carrinho) {
            // Adiciona de 1 a 5 livros aleatórios como itens do carrinho
            $livros = Livro::inRandomOrder()->take(rand(1,5))->get();
            foreach ($livros as $livro) {
                CarrinhoItem::factory()->create([
                    'carrinho_id' => $carrinho->id,
                    'livro_id' => $livro->id,
                    'preco_unitario' => $livro->preco,
                ]);
            }
            });
    }
}
