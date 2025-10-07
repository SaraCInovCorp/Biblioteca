<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserRegistered
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        activity()
        ->causedBy($event->user)
        ->performedOn($event->user)
        ->event('registered')
        ->useLog('auth')
        ->withProperties([
            'ip' => request()->ip(),
            'browser' => request()->header('User-Agent'),
        ])
        ->log('Usuário registrado no sistema');
    }
}
