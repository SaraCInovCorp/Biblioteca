<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EditoraImportacao;
use App\Models\Importacao;
use App\Models\Editora;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EditoraImportacao>
 */
class EditoraImportacaoFactory extends Factory
{
    protected $model = EditoraImportacao::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'importacao_id' => Importacao::factory(),
            'editora_id' => Editora::factory(),
        ];
    }
}
