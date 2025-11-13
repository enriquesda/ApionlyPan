<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Support\Facades\Mail;
//use Mail;
use Illuminate\Support\Facades\Auth;
//use Auth;
use Illuminate\Support\Facades\Storage;

define("LOG_ERRORES", "errores.log");
define("LOG_ERRORES_FIWARE", "errores_Fiware.log");
define("LOG_MENSAJES", "registro.log");
class LogController extends Controller
{
    //
    public static function errores($mensaje)
	{
		$fechaTexto = date('Y/m/d H:i:s');
		$usuario = Auth::user() ? Auth::user()['id'] . ' : ' .  Auth::user()['email'] : '[SIN USUARIO]';

		//Storage::append(LOG_ERRORES, "IP: " . LogController::obtenerIP());
		Storage::append(LOG_ERRORES, $fechaTexto . ' : ' . $usuario . ' : ' . $mensaje);
	}

	public static function erroresFiware($mensaje)
	{
		$fechaTexto = date('d/m/Y H:i:s');

		//Storage::append(LOG_ERRORES, "IP: " . LogController::obtenerIP());
		Storage::append(LOG_ERRORES_FIWARE, $fechaTexto . ' : ' . $mensaje);
	}


}
