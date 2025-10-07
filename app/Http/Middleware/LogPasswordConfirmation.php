<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogPasswordConfirmation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        activity()
            ->causedBy(auth()->user())
            ->event('password_confirmation')
            ->useLog('auth')
            ->withProperties([
                'ip' => $request->ip(),
                'browser' => $request->header('User-Agent'),
            ])
            ->log('Senha confirmada pelo usuário');

        return $response;
    }
}
