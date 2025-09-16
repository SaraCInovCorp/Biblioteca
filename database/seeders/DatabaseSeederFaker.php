<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeederFaker extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            EditoraSeeder::class,
            AutorSeeder::class,
            LivroSeeder::class,
            BookRequestSeeder::class,
        ]);
    }
}
