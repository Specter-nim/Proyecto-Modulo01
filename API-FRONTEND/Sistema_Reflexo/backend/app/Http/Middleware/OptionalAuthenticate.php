<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * Intenta autenticar si hay un token en la peticion. Si no hay, no hace nada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Intenta autenticar si hay token
        Auth::shouldUse('sanctum'); // para asegurar que use sanctum
        $user = Auth::user();

        return $next($request);
    }
}
