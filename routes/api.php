<?php

use App\Http\Controllers\CultivosController;
use App\Http\Controllers\FertilizantesController;
use App\Http\Controllers\FitosanitariosController;
use App\Http\Controllers\ParcelasController;
use App\Http\Controllers\MaquinasController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AperoAgricultorController;
use App\Http\Controllers\AperosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Cors;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });ç
Route::post('usuario/login', 'UsersController@login');

Route::post('usuario/recuperar', 'UsersController@recuperarContrasen');
Route::post ('usuario/registro', 'UsersController@registrarUsuarioAgricultor');



 Route::group(['middleware' => ['jwt.verify', 'cors']], function () {
// Logear en la aplicación

//Route::post('usuario/login', 'UserController@authenticate');
Route::put('usuario/cambiarPass', 'UsersController@cambiarContrasen'); //esto es para que los usuarios puedan modificar su propia contraseña
    Route::group (['middleware' => ['rol:1,2']], function (){
        Route::get('usuario/obtener/{idInicio?}', 'UsersController@obtenerUsuariosConAgricultores');
        Route::post('usuario/nuevo', 'UsersController@crearUsuario');
        Route::put('usuario/editar', 'UsersController@editarUsuario');
        Route::delete('usuario/eliminar/{id}', 'UsersController@eliminarUsuario');
        Route::get('usuario/{id}/relaciones', [UsersController::class, 'obtenerUsuarioConDependientes']);
    });
Route::post('usuario/consentimiento', [UsersController::class, 'aceptarNuevaPolitica']);
Route::get('usuario/buscador/{termino?}', [UsersController::class, 'buscarUsuarios']);
Route::put('usuario/editar2', 'UsersController@editarUsuario2');

Route::post('usuario/valido', 'UsersController@validaUsuario');
Route::post ('usuario/logout', 'UsersController@logout');

});
