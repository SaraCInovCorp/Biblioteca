<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\BookRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite criar uma requisição de livro corretamente', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $livro = Livro::factory()->create(['status' => 'disponivel']); 

    $this->actingAs($user);

    $postData = [
        'data_inicio' => now()->addDay()->toDateString(),
        'data_fim' => now()->addDays(7)->toDateString(),
        'items' => [
            ['livro_id' => $livro->id, 'obs' => 'Pedido de teste'],
        ],
        'user_id' => $user->id,
    ];

    $response = $this->post(route('requisicoes.store'), $postData);

    $response->assertRedirect(route('requisicoes.index'));
    $response->assertSessionHas('success', 'Requisição criada com sucesso!');

    $this->assertDatabaseHas('book_requests', [
        'user_id' => $user->id,
        'data_inicio' => $postData['data_inicio'],
        'data_fim' => $postData['data_fim'],
        'ativo' => true,
    ]);

    $this->assertDatabaseHas('livros', [
        'id' => $livro->id,
        'status' => 'requisitado',
    ]);

    $bookRequest = BookRequest::where('user_id', $user->id)->first();

    expect($bookRequest)->not()->toBeNull();
    expect($bookRequest->items->count())->toBe(1);
    expect($bookRequest->items->first()->livro_id)->toBe($livro->id);
});
