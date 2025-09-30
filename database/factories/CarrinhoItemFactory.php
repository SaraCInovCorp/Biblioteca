<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CarrinhoItem;
use App\Models\Carrinho;
use App\Models\Livro;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarrinhoItem>
 */
class CarrinhoItemFactory extends Factory
{
    protected $model = CarrinhoItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'carrinho_id' => Carrinho::factory(),
            'livro_id' => Livro::factory(),
            'quantidade' => $this->faker->numberBetween(1, 3),
            'preco_unitario' => $this->faker->randomFloat(2, 5, 100),
        ];
    }
}
