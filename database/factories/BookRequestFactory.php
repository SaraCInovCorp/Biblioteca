<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BookRequest;
use App\Models\User;
use Carbon\Carbon;

class BookRequestFactory extends Factory
{
    protected $model = BookRequest::class;

    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-15 days', 'now');
        $endDate = (clone $startDate)->modify('+5 days');

        return [
            // Usuário do tipo 'cidadao'; cria se não existir
            'user_id' => User::where('role', 'cidadao')->inRandomOrder()->first()?->id ?? User::factory()->create(['role' => 'cidadao'])->id,
            'data_inicio' => $startDate,
            'data_fim' => $endDate,
            // Mescla notas preenchidas ou nulas
            'notas' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
            // Mescla ativo true ou false (85% true)
            'ativo' => $this->faker->boolean(85),
        ];
    }
}

