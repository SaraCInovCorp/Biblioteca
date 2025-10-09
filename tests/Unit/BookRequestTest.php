<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use App\Models\BookRequest;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('manual book request creation', function () {
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