<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EncomendaItem;
use App\Models\Encomenda;
use App\Models\Livro;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EncomendaItem>
 */
class EncomendaItemFactory extends Factory
{
    protected $model = EncomendaItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'encomenda_id' => Encomenda::factory(),
            'livro_id' => Livro::factory(),
            'quantidade' => $this->faker->numberBetween(1, 5),
            'preco_unitario' => $this->faker->randomFloat(2, 10, 200),
        ];
    }
}
