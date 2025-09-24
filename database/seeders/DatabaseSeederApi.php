<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeederApi extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            EditoraSeeder::class,
            AutorSeeder::class,
            LivroApiSeeder::class,
            BookRequestSeeder::class,
            BookReviewSeeder::class,
            LivroWaitingListSeeder::class,
        ]);
    }
}
