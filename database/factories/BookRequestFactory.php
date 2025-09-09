<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BookRequest;
use App\Models\User;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookRequest>
 */
class BookRequestFactory extends Factory
{
    protected $model = BookRequest::class;

    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $endDate = (clone $startDate)->modify('+5 days');

        return [
            'user_id' => User::factory(),  // cria usuário se não existir
            'data_inicio' => $startDate,
            'data_fim' => $endDate,
            'notas' => $this->faker->sentence(),
            'ativo' => $this->faker->boolean(80),
        ];
        }
}
