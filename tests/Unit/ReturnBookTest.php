<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\BookRequest;
use App\Models\BookRequestItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite que um utilizador devolva um livro', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $livro = Livro::factory()->create(['status' => 'requisitado']);

    $bookRequest = BookRequest::factory()->for($user)->create(['ativo' => true]);
    $item = BookRequestItem::factory()->for($bookRequest)->for($livro)->create([
        'status' => 'realizada'
    ]);

    $this->actingAs($user);

    $updateData = [
        'data_inicio' => now()->toDateString(),
        'data_fim' => now()->addDays(7)->toDateString(),
        'notas' => 'Devolução teste',
        'items' => [
            [
                'id' => $item->id,
                'livro_id' => $livro->id,
                'status' => 'entregue_ok',
                'obs' => null,
            ],
        ],
    ];

    $response = $this->put(route('requisicoes.update', $bookRequest), $updateData);

    $response->assertRedirect(route('requisicoes.show', $bookRequest));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('book_request_items', [
        'id' => $item->id,
        'status' => 'entregue_ok',
    ]);
});
