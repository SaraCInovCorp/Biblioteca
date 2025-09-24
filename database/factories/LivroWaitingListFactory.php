<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LivroWaitingList;
use App\Models\Livro;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LivroWaitingList>
 */
class LivroWaitingListFactory extends Factory
{
    protected $model = LivroWaitingList::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'livro_id' => Livro::factory(),
            'user_id' => User::where('role', 'cidadao')->inRandomOrder()->first()?->id ?? User::factory()->create(['role' => 'cidadao'])->id,
            'ativo' => true,
            'notificado_em' => null,
        ];
    }
}
