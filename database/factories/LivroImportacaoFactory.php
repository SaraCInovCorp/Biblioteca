<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LivroImportacao;
use App\Models\Importacao;
use App\Models\Livro;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LivroImportacao>
 */
class LivroImportacaoFactory extends Factory
{
    protected $model = LivroImportacao::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'importacao_id' => Importacao::factory(),
            'livro_id' => Livro::factory(),
        ];
    }
}
