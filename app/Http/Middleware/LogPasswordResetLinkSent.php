<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogPasswordResetLinkSent
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
            ->causedBy($request->user() ?? null)
            ->event('password_reset_link_sent')
            ->useLog('auth')
            ->withProperties([
                'ip' => $request->ip(),
                'browser' => $request->header('User-Agent'),
            ])
            ->log('Link de redefinição de senha enviado');
        return $response;
    }
}
