<?php

use App\Models\BookRequest;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

test('factory creates book request', function () {
    $bookRequest = BookRequest::factory()->create();

    $this->assertDatabaseHas('book_requests', [
        'id' => $bookRequest->id,
    ]);
});