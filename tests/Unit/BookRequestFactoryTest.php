<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\BookRequest;

class BookRequestFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_book_request()
    {
        $bookRequest = BookRequest::factory()->create();

        $this->assertDatabaseHas('book_requests', [
            'id' => $bookRequest->id,
        ]);
    }
}
