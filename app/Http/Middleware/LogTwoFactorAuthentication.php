<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogTwoFactorAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        \Log::info('Middleware LogTwoFactorAuthentication executado para IP: '.$request->ip());

        activity()
            ->causedBy($request->user() ?? null)
            ->event('two_factor_auth')
            ->useLog('auth')
            ->withProperties([
                'ip' => $request->ip(),
                'browser' => $request->header('User-Agent'),
            ])
            ->log('Autenticação de dois fatores realizada');

        return $response;
    }
}
