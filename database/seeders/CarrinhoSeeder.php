<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carrinho;
use App\Models\User;

class CarrinhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            // Cada usuário pode ter 1 carrinho ativo
            Carrinho::factory()->create([
                'user_id' => $user->id,
                'status' => 'ativo',
            ]);
        });
    }
}
