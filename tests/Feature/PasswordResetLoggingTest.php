<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class PasswordResetLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordResetGeneratesActivityLog()
    {
        $user = User::factory()->create();

        // Intercepta chamadas para Log::info para confirmar execução do listener
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($msg) use ($user) {
                return str_contains($msg, 'Capturou evento PasswordReset') && str_contains($msg, (string)$user->id);
            });

        // Dispara o evento, que deve ativar o listener que registra atividade
        event(new PasswordReset($user));

        // Confirma que o registro de atividade foi criado no banco
        $this->assertDatabaseHas('activity_log', [
            'causer_id' => $user->id,
            'event' => 'reset',
            'log_name' => 'auth',
        ]);
    }
}
