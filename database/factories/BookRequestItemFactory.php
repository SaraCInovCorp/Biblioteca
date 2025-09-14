<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BookRequestItem;
use App\Models\Livro;
use App\Models\BookRequest;
use Carbon\Carbon;

class BookRequestItemFactory extends Factory
{
    protected $model = BookRequestItem::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['realizada', 'entregue_ok', 'entregue_obs', 'nao_entregue', 'cancelada']);

        return [
            'data_real_entrega' => null,
            'dias_decorridos' => null,
            'status' => $status,
            'obs' => $this->faker->boolean(30) ? $this->faker->sentence() : null, 
            'livro_id' => Livro::factory()->state(['status' => 'disponivel']),
            'book_request_id' => BookRequest::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (BookRequestItem $item) {
            $livro = $item->livro;
            $livro->status = 'requisitado';
            $livro->save();

            $entregueStatuses = ['entregue_ok', 'entregue_obs', 'cancelada'];

            if (in_array($item->status, $entregueStatuses)) {
                $startDate = $item->bookRequest->data_inicio;
                $daysElapsed = rand(0, 30);
                $realDeliveryDate = Carbon::parse($startDate)->addDays($daysElapsed);

                $item->data_real_entrega = $realDeliveryDate;
                $item->dias_decorridos = $daysElapsed;
                $item->save();

                // Se entregue, livro volta para disponível
                if ($item->status != 'nao_entregue') {
                    $livro->status = 'disponivel';
                    $livro->save();
                }
            }
        });
    }
}

