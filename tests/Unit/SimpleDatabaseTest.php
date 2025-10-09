<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user factory works', function () {
    $user = User::factory()->create();
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});