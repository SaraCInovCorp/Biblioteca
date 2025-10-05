<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Carrinho;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Carrinho>
 */
class CarrinhoFactory extends Factory
{
    protected $model = Carrinho::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-10 days', 'now');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        return [
            'user_id' => User::factory(),
            'status' => 'ativo',
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'lembrete_enviado_em' => $this->faker->optional(0.15)->dateTimeBetween('-2 days', 'now'),
            'lembrete_enviado_para' => $this->faker->optional(0.15)->safeEmail,
        ];
    }
}
