<?php

namespace App\Http\Middleware;

use Closure;

use Exception;
use Storage;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth ;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		try
		{
			
			$user = JWTAuth::parseToken()->authenticate();
			
		}
		catch (Exception $e)
		{
			if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
				return response()->json(['status' => 'Token is Invalid']);
			}else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
				return response()->json(['status' => 'Token is Expired']);
			}else{
				return response()->json(['status' => 'Authorization Token not found']);
			}
		}
		// Inyecta el usuario autenticado en la request
        $request->attributes->set('auth_userJWT', $user);
		//dd($request);
		return $next($request);
	}
}
