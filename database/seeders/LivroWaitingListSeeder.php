<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LivroWaitingList;
use App\Models\Livro;
use App\Models\User;

class LivroWaitingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $livros = Livro::all();
        $users = User::where('role', 'cidadao')->get();

        foreach ($livros as $livro) {
            $randomUsers = $users->random(rand(0, 3));
            foreach ($randomUsers as $user) {
                LivroWaitingList::updateOrCreate(
                    ['livro_id' => $livro->id, 'user_id' => $user->id, 'ativo' => true],
                    ['notificado_em' => null]
                );
            }
        }
    }
}
