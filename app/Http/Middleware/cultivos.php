<?php

namespace App\Http\Middleware;

use App\Models\CultivoParcela;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth ;
use Exception;


class cultivos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{

        
        $user = JWTAuth::parseToken()->authenticate();

        if (($user->rol == 1 || $user->rol == 2) ){
            return $next($request);
        }
        $esPropietario =$this->comprobarPropietario($user, $request);
			
			if ( $esPropietario){
                return $next($request);
			}
            
            return response()->json([
                "mensaje" => "No tienes permisos para acceder a este recurso."
            ], 403);
        }catch (Exception $e){

            return response()->json (["mensaje"=> $e->getMessage() ],300);
        }


    }
  
  /**
     * Comprobar si el usuario es propietario del recurso.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function comprobarPropietario($user, $request)
    {
        // Obtener el ID del cultivo de la solicitud
        $idCultivo = $request->route('id'); // Suponiendo que el ID viene en la URL como un parámetro
        // Verificar si el cultivo pertenece al usuario
        return CultivoParcela::where('id', $idCultivo)
            ->whereHas('parcela', function ($query) use ($user) {
                $query->where('id_agricultor', $user->id);
            })->exists();
    }

}
