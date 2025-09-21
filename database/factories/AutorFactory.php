<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Autor;
use App\Models\Autor_Livro;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Autor>
 */
class AutorFactory extends Factory
{
    protected $model = Autor::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'foto_url' => 'https://picsum.photos/150/200?image=' . $this->faker->numberBetween(1, 1000),
            'origem' => 'seeder',
            'user_id' => 1,
        ];
    }
}
