<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Autor_Livro;
use App\Models\Autor;
use App\Models\Livro;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Autor_Livro>
 */
class AutorLivroFactory extends Factory
{
    protected $model = Autor_Livro::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'autor_id' => \App\Models\Autor::factory(),
            'livro_id' => \App\Models\Livro::factory(),
        ];
    }

    

}
