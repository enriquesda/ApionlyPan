<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param //number [] se espera un vector de varios roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$rolesPermitidos) //...$roles-> esto permite enviar varios roles indicando un solo parametro?
    {
        $user = auth()->user();
    
        if (!$user || !in_array($user->rol, $rolesPermitidos)) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }
    
        return $next($request);
    }
}
