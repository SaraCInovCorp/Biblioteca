<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\Importacao;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Importacao>
 */
class ImportacaoFactory extends Factory
{
    protected $model = Importacao::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'api' => $this->faker->randomElement(['google_books', 'import_seeder']),
            'imported_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
