<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\BookRequest;

class BookRequestTestFactory extends TestCase
{
    use RefreshDatabase;

    public function test_manual_book_request_creation()
    {
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
    }
}
