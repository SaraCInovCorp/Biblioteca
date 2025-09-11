<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->withPersonalTeam()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
        ]);

        User::factory()->count(10)->withPersonalTeam()->create();

        $this->call([
            EditoraSeeder::class,
            AutorSeeder::class,
            LivroSeeder::class,
            BookRequestSeeder::class,
        ]);
    }
}

