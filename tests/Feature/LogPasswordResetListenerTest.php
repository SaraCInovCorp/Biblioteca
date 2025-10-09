<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('password reset listener is called', function () {
    $user = User::factory()->create();

    Log::spy();

    event(new PasswordReset($user));

    Log::shouldHaveReceived('info')
        ->atLeast()->once()
        ->withArgs(function ($msg) use ($user) {
            return str_contains($msg, 'LogPasswordReset') &&
                   str_contains($msg, (string) $user->id);
        });
});