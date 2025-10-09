<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('password reset generates activity log', function () {
    $user = User::factory()->create();

   Log::spy();

    event(new PasswordReset($user));

    $this->assertDatabaseHas('activity_log', [
        'causer_id' => $user->id,
        'event' => 'reset',
        'log_name' => 'auth',
    ]);
});