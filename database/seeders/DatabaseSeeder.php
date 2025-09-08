<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\Livro;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
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
