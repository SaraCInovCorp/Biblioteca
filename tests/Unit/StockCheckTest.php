<?php

use App\Models\User;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('impede requisição de livro sem stock disponível', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $livro = Livro::factory()->create(['status' => 'requisitado']); 

    $this->actingAs($user);

    $postData = [
        'data_inicio' => now()->addDay()->toDateString(),
        'data_fim' => now()->addDays(7)->toDateString(),
        'items' => [
            ['livro_id' => $livro->id, 'obs' => 'Livro sem stock'],
        ],
        'user_id' => $user->id,
    ];

    $response = $this->post(route('requisicoes.store'), $postData);

    $response->assertSessionHasErrors('items');
});
