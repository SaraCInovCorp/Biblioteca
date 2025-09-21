<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Editora;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Editora>
 */
class EditoraFactory extends Factory
{
    protected $model = Editora::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->company(),
            'logo_url' => 'https://picsum.photos/150/200?image=' . $this->faker->numberBetween(1, 1000),
            'origem' => 'seeder', 
            'user_id' => 1,
        ];
    }
}
