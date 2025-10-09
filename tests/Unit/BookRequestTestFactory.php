<?php

use App\Models\User;
use App\Models\BookRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('cria uma requisição de livro manualmente', function () {
    $user = User::factory()->create();

    $bookRequest = BookRequest::create([
        'user_id' => $user->id,
        'data_inicio' => now(),
        'data_fim' => now()->addDay(),
        'ativo' => true,
        'notas' => 'Teste manual',
    ]);

    $this->assertDatabaseHas('book_requests', [
        'id' => $bookRequest->id,
        'user_id' => $user->id,
    ]);
});
