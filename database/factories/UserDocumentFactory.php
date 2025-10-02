<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDocument;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDocument>
 */
class UserDocumentFactory extends Factory
{
    protected $model = UserDocument::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tiposDocumento = ['BI', 'CC', 'Passaporte'];
        $generos = ['Masculino', 'Feminino', 'Outro'];

        return [
            'data_nascimento' => $this->faker->date(),
            'tipo_documento' => $this->faker->randomElement($tiposDocumento),
            'numero_documento' => $this->faker->bothify('??######'),
            'data_emissao' => $this->faker->date(),
            'data_validade' => $this->faker->date(),
            'entidade_emissora' => $this->faker->company(),
            'nacionalidade' => 'Portugal',
            'genero' => $this->faker->randomElement($generos),
        ];
    }
}
