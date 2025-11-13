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
        //Gestion de fitosanitarios por los administradores y superadministradores con nueva forma de definir urls
        Route::post('fitosanitarios/nuevo', [FitosanitariosController::class, 'nuevoFitosanitario']);
        Route::put('fitosanitarios/editar/{id}', [FitosanitariosController::class, 'editarFitosanitario']);
        Route::delete('fitosanitarios/eliminar/{id}', [FitosanitariosController::class, 'eliminarFitosanitario']);
        //Gestion de maquinaria
        Route::post ('maquinaria/nueva', [MaquinasController::class, 'nuevaMaquina']);
        Route::put ('maquinaria/editar', [MaquinasController::class, 'editarMaquina']);
        Route::delete ('maquinaria/eliminar/{id}', [MaquinasController::class, 'eliminarMaquina']);
        Route::post ('maquinaria/subirImagen/{id}', [MaquinasController::class, 'subirImagen']);

        //gestion administrativa de fertilizantes:
        Route::post ('fertilizantes/crear', [FertilizantesController::class, 'crearFertilizante']);//crear modelo
        Route::put ('fertilizantes/editar', [FertilizantesController::class, 'editarFertilizante']);//editar fertilizante
        Route::delete ('fertilizantes/eliminar/{id}',  [FertilizantesController::class, 'eliminarFertilizante']);
        // CRUD de aperos
         Route::get('aperos', [AperosController::class, 'obtenerTodos']);
        Route::post('aperos/nuevo', [AperosController::class, 'nuevoApero']);
        Route::put('aperos/editar/{id}', [AperosController::class, 'editarApero']);
        Route::delete('aperos/eliminar/{id}', [AperosController::class, 'borrarApero']);
        Route::post('aperos/subirImagen/{id}', [AperosController::class, 'subirImagen']);
    });
Route::post('usuario/consentimiento', [UsersController::class, 'aceptarNuevaPolitica']);
Route::get('usuario/buscador/{termino?}', [UsersController::class, 'buscarUsuarios']);
Route::put('usuario/editar2', 'UsersController@editarUsuario2');

Route::post('usuario/valido', 'UsersController@validaUsuario');
Route::post ('usuario/logout', 'UsersController@logout');



//rutas del controlador de agricultores
Route::get('agricultor/obtener', 'AgricultoresController@obtener');
Route::get('agricultor/obtener/{id}', 'AgricultoresController@obtenerPorId');
Route::get('agricultor/obtenerMaquinas', 'AgricultoresController@obtenerTodasLasMaquinas');
Route::get('agricultor/obtenerAperos', 'AgricultoresController@obtenerTodosLosAperos');
Route::get('agricultor/obtenerMyA/{idAgricultor}', 'AgricultoresController@obtenerMaquinasyAperosAgricultor'); //obtener maquinas y aperos de un agricultor
Route::post('agricultor/nuevo', 'AgricultoresController@crear');
Route::post('agricultor/addMaquina/{idAgricultor}/{idMaquina}', 'AgricultoresController@agregarMaquina');
Route::post('agricultor/addApero/{idAgricultor}/{idApero}', 'AgricultoresController@agregarApero');
Route::put('agricultor/editar', 'AgricultoresController@editar');
Route::delete('agricultor/eliminar/apero/{idAgricultor}/{idApero}', 'AgricultoresController@eliminarApero');
Route::delete('agricultor/eliminar/maquina/{idAgricultor}/{idMaquina}', 'AgricultoresController@eliminarMaquina');
Route::delete('agricultor/eliminar/{id}', 'AgricultoresController@borrar');

//rutas del controlador de parcelas
//Route::get('parcelas', 'ParcelasController@obtenerTodas');
Route::get('parcelas/agricultor/{idAgricultor}', 'ParcelasController@obtenerParcelasAgricultor');
Route::post('parcelas/nueva', 'ParcelasController@crearParcela');
Route::post ('parcelas/agregarFiltros/{idParcela}', 'ParcelasController@agregarFiltrosParcela');
Route::put ('parcelas/editarFiltros/{idParcela}', 'ParcelasController@actualizarFiltrosParcela'); //redibe un array con los nuevos filtros eliminando los que hubiera previamente
Route::put('parcelas/editar', 'ParcelasController@editar');
Route::delete('parcelas/eliminar/{id}', 'ParcelasController@borrar');
Route::get('municipios/provincia/{idProvincia}', 'ParcelasController@obtenerMunicipios');
Route::get('provincias', 'ParcelasController@obtenerProvincias');
Route::get('filtros', 'ParcelasController@obtenerFiltros');
Route::get('/parcelas/tanques/{idParcela}', [ParcelasController::class, 'obtenerTanques']);
Route::post('/parcelas/agregarTanque', [ParcelasController::class, 'crearTanque']);
Route::put('/parcelas/actualizarTanque/{idTanque}', [ParcelasController::class, 'editarTanque']);
Route::delete('/parcelas/eliminarTanque/{idTanque}', [ParcelasController::class, 'eliminarTanque']);

//rutas del controlador de Cultivos ( cultivo_parcela)

Route::get('cultivos_parcelas', 'CultivosController@obtenerTodas');
Route::get('cultivos/parcela/{id}', 'CultivosController@obtenerResumenCultivosParcela');

    Route::group(['middleware' => ['cultMiddle']], function () { //esto hay que sustituirlo por comprobaciones de usuario propietario con el nuevo helper en el modelo de usuario
        Route::get('cultivo/{id}', 'CultivosController@obtenerCultivoPorId');
        Route::delete('cultivo/eliminar/{id}', 'CultivosController@eliminarCultivo');
        Route::put('cultivo/editar/{id}', 'CultivosController@editarCultivoParcela');
        Route::get ('cultivo/calculos/{id}', 'CultivosController@generarInformeCultivo');
        Route::post ('cultivo/informe/{id}', 'CultivosController@agregarInforme');
        Route::get ('cultivo/obtenerInforme/{id}', 'CultivosController@obtenerArchivo');
    });


Route::get ('cultivos/tipos', 'CultivosController@obtenerTiposCultivo');
Route::post('cultivo/nuevo', 'CultivosController@crearCultivoParcela');
Route::get('cultivos/seleccionables', [CultivosController::class, 'obtenerSeleccionables']);
Route::get('cultivos/enviarEmail/{correo}/{cultivoId}', [CultivosController::class, 'enviarEmail']);

Route::get('fitosanitarios/obtener', [FitosanitariosController::class, 'obtenerTiposFitosanitarios']);
Route::get ('fitosanitarios/aplicaciones/{agricultorId}', [FitosanitariosController::class, 'obtnerAplicacionesDeAgricultor']);
Route::post ('fitosanitarios/aplicaciones/nueva', [FitosanitariosController::class, 'addAplicacion']); //para añadidos unitarios de un unico fitosanitario y no varios de golpe sobre un cultivo
Route::delete ('fitosanitarios/aplicaciones/eliminar', [FitosanitariosController::class, 'eliminarAplicacion']);
Route::put ('fitosanitarios/aplicaciones/editar', [FitosanitariosController::class,'addAplicaciones']); //permie editar quitar o añadir aplicaciones de fitosanitarios a un cultivo

//Rutas de gestión de fertilizantes:
Route::get ('fertilizantes/obtenerModelos', [FertilizantesController::class, 'obtenerModelos']);
Route::get ('fertilizantes/obtenerTanques/{id_agricultor}', [FertilizantesController::class, 'obtenerTanqueFertilizanteYLecturas']);
Route::get ('fertilizantes/obtenerAplicaciones/{id_agricultor}', [FertilizantesController::class, 'obtenerAplicacionesAgricultor']);
Route::post ('fertilizantes/addAplicacion', [FertilizantesController::class, 'addAplicacion']);//crear modelo
Route::delete ('fertilizantes/eliminarAplicacion/{id}',  [FertilizantesController::class, 'eliminarAplicacionFertilizante']);

/**Rutas de maquinaria */
Route::get ('maquinaria/all', [MaquinasController::class, 'obtenerMaquinaria']);
Route::get('maquinaria/agricultor/{idAgricultor}', [MaquinasController::class, 'obtenerMaquinariaAgricultor']);
Route::post('maquinaria/agricultor/nueva', [MaquinasController::class, 'crearMaquinaAgricultor']);
Route::put('maquinaria/agricultor/editar', [MaquinasController::class, 'editarMaquinaAgricultor']);
Route::delete('maquinaria/agricultor/eliminar/{id}', [MaquinasController::class, 'eliminarMaquinaAgricultor']);

// CRUD de aperos asociados a agricultor
Route::get('apero/agricultor/{idAgricultor}', [AperoAgricultorController::class, 'index']);
Route::post('apero/agricultor/nuevo', [AperoAgricultorController::class, 'store']);
Route::put('apero/agricultor/editar/{id}', [AperoAgricultorController::class, 'update']);
Route::delete('apero/agricultor/eliminar/{id}', [AperoAgricultorController::class, 'destroy']);



});
