<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Encomenda;
use App\Models\User;
use App\Models\Endereco;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Encomenda>
 */
class EncomendaFactory extends Factory
{
    protected $model = Encomenda::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'endereco_id' => Endereco::factory(),
            'status' => 'pendente',
            'total' => $this->faker->randomFloat(2, 20, 500),
            'stripe_payment_intent_id' => null,
            'payment_status' => 'pending',
        ];
    }
}
