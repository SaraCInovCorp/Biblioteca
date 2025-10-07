<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class LogPasswordResetListenerTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordResetListenerIsCalled()
    {
        $user = User::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($msg) use ($user) {
                return str_contains($msg, 'Capturou evento PasswordReset') && str_contains($msg, (string) $user->id);
            });

        event(new PasswordReset($user));
    }
}
