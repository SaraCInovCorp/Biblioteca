<?php

use App\Models\User;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class); // <-- Roda as migrations antes de cada teste

it('the application returns a successful response', function () {
    $user = User::factory()->create();

    Livro::factory()->count(6)->create([
        'user_id' => $user->id, 
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
});
