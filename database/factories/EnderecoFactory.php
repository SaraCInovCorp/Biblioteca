<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Endereco;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Endereco>
 */
class EnderecoFactory extends Factory
{
    protected $model = Endereco::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tipo' => $this->faker->randomElement(['pagamento', 'entrega']),
            'logradouro' => $this->faker->streetName(),
            'numero' => $this->faker->buildingNumber(),
            'andereco' => $this->faker->secondaryAddress(),
            'freguesia' => $this->faker->citySuffix(),
            'localidade' => $this->faker->city(),
            'distrito' => $this->faker->randomElement([
                'Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra',
                'Évora', 'Faro', 'Guarda', 'Leiria', 'Lisboa', 'Portalegre', 
                'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real', 'Viseu',
            ]),
            'codigo_postal' => $this->faker->postcode(),
            'pais' => 'Portugal',
            'telemovel' => $this->faker->phoneNumber(),
        ];
    }
}
