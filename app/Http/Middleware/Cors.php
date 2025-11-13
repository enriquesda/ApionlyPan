<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
		return $next($request)
		->header('Access-Control-Allow-Origin', '*')
		->header('Access-Control-Allow-Methods', '*')
		->header('Access-Control-Allow-Headers', '*')
		->header ('Access-Control-Expose-Headers', 'Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Allow-Headers');
	}
}
