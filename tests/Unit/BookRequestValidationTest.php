<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('não permite criar uma requisição sem livro válido', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $this->actingAs($user);

    $postData = [
        'data_inicio' => now()->addDay()->toDateString(),
        'data_fim' => now()->addDays(7)->toDateString(),
        'items' => [
            ['livro_id' => 999999, 'obs' => 'Livro inválido'], 
        ],
        'user_id' => $user->id,
    ];

    $response = $this->post(route('requisicoes.store'), $postData);

    $response->assertSessionHasErrors('items.0.livro_id');
});
