<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Importacao;
use App\Models\User;
use App\Models\Livro;
use App\Models\Editora;
use App\Models\AutorImportacao;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutorImportacao>
 */
class AutorImportacaoFactory extends Factory
{
    protected $model = AutorImportacao::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'importacao_id' => Importacao::factory(),
            'autor_id' => Autor::factory(),
        ];
    }
}
