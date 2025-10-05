<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Encomenda;
use App\Models\User;
use App\Models\Endereco;
use App\Models\Carrinho;

class EncomendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            $endereco = Endereco::where('user_id', $user->id)->first();
            $carrinho = Carrinho::where('user_id', $user->id)
            ->where('status', 'ativo')
            ->first();

            if ($endereco && $carrinho) {
                Encomenda::factory()->count(rand(1,2))->create([
                    'user_id' => $user->id,
                    'endereco_id' => $endereco->id,
                    'carrinho_id' => $carrinho->id,
                ]);
            }
        });
    }
}
