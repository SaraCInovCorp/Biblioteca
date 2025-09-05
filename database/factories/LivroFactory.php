<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\Autor_Livro;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livro>
 */
class LivroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(3), 
            'isbn' => $this->faker->unique()->isbn13(), 
            'editora_id' => Editora::factory(), 
            'bibliografia' => $this->faker->paragraph(),
            'capa_url' => 'https://picsum.photos/150/200?random=' . rand(1, 50),
            'preco' => $this->faker->randomFloat(2, 10, 200), 
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Livro $livro) {
            // Associa entre 1 a 3 autores aleatórios a cada livro criado
            $autorIds = Autor::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $livro->autores()->attach($autorIds);
        });
    }
}
