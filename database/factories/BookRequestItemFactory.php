<?php

namespace Database\Factories;
use App\Models\BookRequestItem;
use App\Models\Livro;
use App\Models\BookRequest;


use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookRequestItem>
 */
class BookRequestItemFactory extends Factory
{
    protected $model = BookRequestItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-30 days', 'now');
        return [
            'data_real_entrega' => $startDate,
            'dias_decorridos' => 0,
            'status' => $this->faker->randomElement(['cancelada', 'realizada', 'entregue_ok', 'entregue_obs', 'nao_entregue']),
            'livro_id' => Livro::factory(),
            'book_request_id' => BookRequest::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (BookRequestItem $item) {
            $startDate = $item->bookRequest->data_inicio;

            $daysElapsed = rand(0, 30);

            $realDeliveryDate = Carbon::parse($startDate)->addDays($daysElapsed);

            $item->data_real_entrega = $realDeliveryDate;
            $item->dias_decorridos = $daysElapsed;

            $item->save();
        });
    }
}
