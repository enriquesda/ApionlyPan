<?php

namespace App\Http\Controllers;

use App\Models\FertilizanteModel;
use App\Models\AplicacionFertilizante;
use App\Models\TanqueFertilizante;
use App\Models\CultivoParcela;
use Illuminate\Validation\ValidationException;
use App\Models\Parcela;
use App\Models\Fertilizante;
use Illuminate\Http\Request;
use Exception;

class FertilizantesController extends Controller
{
    // CRUD TanqueFertilizante
    public function indexTanques(Request $request)
    {
        $query = TanqueFertilizante::query();
        if ($request->has('id_parcela')) {
            $query->where('id_parcela', $request->id_parcela);
        }
        $tanques = $query->get();
        return response()->json(['data' => $tanques], 200);
    }

    public function showTanque($id)
    {
        $tanque = TanqueFertilizante::find($id);
        if (!$tanque) {
            return response()->json(['message' => 'Tanque de fertilizante no encontrado.'], 404);
        }
        return response()->json(['data' => $tanque], 200);
    }

    public function storeTanque(Request $request)
    {
        try {
            $request->validate([
                'id_sensor'      => 'required|string|max:255',
                'id_agricultor'  => 'required|integer|exists:users,id',
                'alto'           => 'nullable|numeric|min:0',
                'ancho'          => 'nullable|numeric|min:0',
                'largo'          => 'nullable|numeric|min:0',
                'volumen'        => 'nullable|numeric|min:0',
                'porcentaje_P'   => 'nullable|numeric|min:0|max:100',
                'porcentaje_N'   => 'nullable|numeric|min:0|max:100',
                'porcentaje_K'   => 'nullable|numeric|min:0|max:100',
                'nombre_fert'    => 'nullable|string|max:255',
                'id_parcela'     => 'required|integer|exists:parcelas,id',
            ], [
                'id_sensor.required'      => 'El campo id_sensor es obligatorio.',
                'id_sensor.string'        => 'El campo id_sensor debe ser una cadena.',
                'id_sensor.max'           => 'El campo id_sensor no puede superar los 255 caracteres.',
                'id_agricultor.required'  => 'El campo id_agricultor es obligatorio.',
                'id_agricultor.integer'   => 'El campo id_agricultor debe ser un número entero.',
                'id_agricultor.exists'    => 'El agricultor especificado no existe.',
                'alto.numeric'            => 'El campo alto debe ser numérico.',
                'alto.min'                => 'El campo alto no puede ser negativo.',
                'ancho.numeric'           => 'El campo ancho debe ser numérico.',
                'ancho.min'               => 'El campo ancho no puede ser negativo.',
                'largo.numeric'           => 'El campo largo debe ser numérico.',
                'largo.min'               => 'El campo largo no puede ser negativo.',
                'volumen.numeric'         => 'El campo volumen debe ser numérico.',
                'volumen.min'             => 'El campo volumen no puede ser negativo.',
                'porcentaje_P.numeric'    => 'El campo porcentaje_P debe ser numérico.',
                'porcentaje_P.min'        => 'El campo porcentaje_P no puede ser negativo.',
                'porcentaje_P.max'        => 'El campo porcentaje_P no puede superar 100.',
                'porcentaje_N.numeric'    => 'El campo porcentaje_N debe ser numérico.',
                'porcentaje_N.min'        => 'El campo porcentaje_N no puede ser negativo.',
                'porcentaje_N.max'        => 'El campo porcentaje_N no puede superar 100.',
                'porcentaje_K.numeric'    => 'El campo porcentaje_K debe ser numérico.',
                'porcentaje_K.min'        => 'El campo porcentaje_K no puede ser negativo.',
                'porcentaje_K.max'        => 'El campo porcentaje_K no puede superar 100.',
                'nombre_fert.string'      => 'El campo nombre_fert debe ser una cadena.',
                'nombre_fert.max'         => 'El campo nombre_fert no puede superar los 255 caracteres.',
                'id_parcela.required'     => 'El campo id_parcela es obligatorio.',
                'id_parcela.integer'      => 'El campo id_parcela debe ser un número entero.',
                'id_parcela.exists'       => 'La parcela especificada no existe.'
            ]);
            $tanque = TanqueFertilizante::create($request->all());
            return response()->json(['message' => 'Tanque de fertilizante creado correctamente.', 'data' => $tanque], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        }
    }

    public function updateTanque(Request $request, $id)
    {
        $tanque = TanqueFertilizante::find($id);
        if (!$tanque) {
            return response()->json(['message' => 'Tanque de fertilizante no encontrado.'], 404);
        }
        try {
            $request->validate([
                'id_sensor'      => 'sometimes|required|string|max:255',
                'id_agricultor'  => 'sometimes|required|integer|exists:users,id',
                'alto'           => 'nullable|numeric|min:0',
                'ancho'          => 'nullable|numeric|min:0',
                'largo'          => 'nullable|numeric|min:0',
                'volumen'        => 'nullable|numeric|min:0',
                'porcentaje_P'   => 'nullable|numeric|min:0|max:100',
                'porcentaje_N'   => 'nullable|numeric|min:0|max:100',
                'porcentaje_K'   => 'nullable|numeric|min:0|max:100',
                'nombre_fert'    => 'nullable|string|max:255',
                'id_parcela'     => 'sometimes|required|integer|exists:parcelas,id',
            ], [
                'id_sensor.required'      => 'El campo id_sensor es obligatorio.',
                'id_sensor.string'        => 'El campo id_sensor debe ser una cadena.',
                'id_sensor.max'           => 'El campo id_sensor no puede superar los 255 caracteres.',
                'id_agricultor.required'  => 'El campo id_agricultor es obligatorio.',
                'id_agricultor.integer'   => 'El campo id_agricultor debe ser un número entero.',
                'id_agricultor.exists'    => 'El agricultor especificado no existe.',
                'alto.numeric'            => 'El campo alto debe ser numérico.',
                'alto.min'                => 'El campo alto no puede ser negativo.',
                'ancho.numeric'           => 'El campo ancho debe ser numérico.',
                'ancho.min'               => 'El campo ancho no puede ser negativo.',
                'largo.numeric'           => 'El campo largo debe ser numérico.',
                'largo.min'               => 'El campo largo no puede ser negativo.',
                'volumen.numeric'         => 'El campo volumen debe ser numérico.',
                'volumen.min'             => 'El campo volumen no puede ser negativo.',
                'porcentaje_P.numeric'    => 'El campo porcentaje_P debe ser numérico.',
                'porcentaje_P.min'        => 'El campo porcentaje_P no puede ser negativo.',
                'porcentaje_P.max'        => 'El campo porcentaje_P no puede superar 100.',
                'porcentaje_N.numeric'    => 'El campo porcentaje_N debe ser numérico.',
                'porcentaje_N.min'        => 'El campo porcentaje_N no puede ser negativo.',
                'porcentaje_N.max'        => 'El campo porcentaje_N no puede superar 100.',
                'porcentaje_K.numeric'    => 'El campo porcentaje_K debe ser numérico.',
                'porcentaje_K.min'        => 'El campo porcentaje_K no puede ser negativo.',
                'porcentaje_K.max'        => 'El campo porcentaje_K no puede superar 100.',
                'nombre_fert.string'      => 'El campo nombre_fert debe ser una cadena.',
                'nombre_fert.max'         => 'El campo nombre_fert no puede superar los 255 caracteres.',
                'id_parcela.required'     => 'El campo id_parcela es obligatorio.',
                'id_parcela.integer'      => 'El campo id_parcela debe ser un número entero.',
                'id_parcela.exists'       => 'La parcela especificada no existe.'
            ]);
            $tanque->update($request->all());
            return response()->json(['message' => 'Tanque de fertilizante actualizado correctamente.', 'data' => $tanque], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        }
    }

    public function destroyTanque($id)
    {
        $tanque = TanqueFertilizante::find($id);
        if (!$tanque) {
            return response()->json(['message' => 'Tanque de fertilizante no encontrado.'], 404);
        }
        $tanque->delete();
        return response()->json(['message' => 'Tanque de fertilizante eliminado correctamente.'], 200);
    }


    //
    public function esAdminOPropietario($obj){
    
        if (auth()->user()->esPropietarioDe($obj)==true){
            return true;    
           }else{
             abort (403, "Parece que no tiene permisos para acceder a este recurso");
           }
    }
    public function obtenerModelos (){
        return response()->json (['data'=>FertilizanteModel::all()], 200);
    }
    public function crearFertilizante(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:255',
            'porcentaje_N'  => 'required|numeric|min:0|max:100',
            'porcentaje_P'  => 'required|numeric|min:0|max:100',
            'porcentaje_K'  => 'required|numeric|min:0|max:100',
        ]);
        
        $fertilizante = FertilizanteModel::create($request->only(['nombre', 'porcentaje_N', 'porcentaje_P', 'porcentaje_K']));

        return response()->json(['message' => 'Fertilizante creado correctamente.', 'data' => $fertilizante], 201);
    }
    public function editarFertilizante(Request $request)
    {
        try{
            $request->validate([
                'id'            => 'required|numeric',
                'nombre'        => 'sometimes|required|string|max:255',
                'porcentaje_N'  => 'sometimes|required|numeric|min:0|max:100',
                'porcentaje_P'  => 'sometimes|required|numeric|min:0|max:100',
                'porcentaje_K'  => 'sometimes|required|numeric|min:0|max:100',
            ],[
                'id.required' => 'Debe proporcionar un ID del fertilizante.',
                'id.numeric' => 'El ID del fertilizante debe ser un número.',
            
                'nombre.required' => 'Debe proporcionar un nombre para el fertilizante.',
                'nombre.string' => 'El nombre del fertilizante debe ser una cadena de texto.',
                'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            
                'porcentaje_N.required' => 'Debe proporcionar el porcentaje de Nitrógeno (N).',
                'porcentaje_N.numeric' => 'El porcentaje de Nitrógeno debe ser un número.',
                'porcentaje_N.min' => 'El porcentaje de Nitrógeno no puede ser negativo.',
                'porcentaje_N.max' => 'El porcentaje de Nitrógeno no puede superar el 100%.',
            
                'porcentaje_P.required' => 'Debe proporcionar el porcentaje de Fósforo (P).',
                'porcentaje_P.numeric' => 'El porcentaje de Fósforo debe ser un número.',
                'porcentaje_P.min' => 'El porcentaje de Fósforo no puede ser negativo.',
                'porcentaje_P.max' => 'El porcentaje de Fósforo no puede superar el 100%.',
            
                'porcentaje_K.required' => 'Debe proporcionar el porcentaje de Potasio (K).',
                'porcentaje_K.numeric' => 'El porcentaje de Potasio debe ser un número.',
                'porcentaje_K.min' => 'El porcentaje de Potasio no puede ser negativo.',
                'porcentaje_K.max' => 'El porcentaje de Potasio no puede superar el 100%.',
            ]);
           
            $fertilizante = FertilizanteModel::findOrFail($request->id);

        
            $fertilizante->update($request->only(['nombre', 'porcentaje_N', 'porcentaje_P', 'porcentaje_K']));
            return response()->json(['message' => 'Fertilizante actualizado correctamente.', 'data' => $fertilizante], 200);
        }catch (ValidationException $e) {
            // Capturamos la excepción de validación
            $errors = $e->validator->errors()->all(); // Obtener solo los mensajes de error como un array

            // Retornamos la respuesta en el formato deseado
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        }
    }
    public function eliminarFertilizante($id)
    {
        $fertilizante = FertilizanteModel::findOrFail($id);
        $fertilizante->delete();

        return response()->json(['message' => 'Fertilizante eliminado correctamente.'], 200);
    }

    public function addAplicacion(Request $request) {
        try{
            $request->validate([
                'id_cultivo'    => 'required|exists:cultivo_parcela,id',
                'id_agricultor' => 'nullable|exists:users,id',
                'fecha'         => 'nullable|date',
                'kg_ha'         => 'required|numeric|min:0',
                'km'            => 'required|numeric|min:0',
            
                // Unidades y/o porcentaje para N
                'unidades_N'    => 'nullable|numeric|min:0',
                'porcentaje_N'  => 'nullable|numeric|min:0|max:100',
            
                // Unidades y/o porcentaje para P
                'unidades_P'    => 'nullable|numeric|min:0',
                'porcentaje_P'  => 'nullable|numeric|min:0|max:100',
            
                // Unidades y/o porcentaje para K
                'unidades_K'    => 'nullable|numeric|min:0',
                'porcentaje_K'  => 'nullable|numeric|min:0|max:100',
            
            ], [
                'id_cultivo.required'    => 'Debe indicar el cultivo.',
                'id_cultivo.exists'      => 'El cultivo no existe.',
                'id_agricultor.required' => 'Debe indicar el agricultor.',
                'id_agricultor.exists'   => 'El agricultor no existe.',
                'kg_ha.required'         => 'Debe indicar los kg/ha.',
                'km.required'            => 'Debe indicar los kilómetros recorridos.',
            ]);
           
            $cultivo = CultivoParcela::with('parcela')->findOrFail($request->id_cultivo);
            $id_agricultor = $cultivo->parcela->id_agricultor;

            // Verificar si el usuario autenticado es el propietario o un admin
            $user = auth()->user();
            if (!$user->esAdmin() && $user->id !== $id_agricultor) {
                return response()->json(['message' => 'No tienes permiso para añadir aplicaciones a este cultivo.'], 403);
            }
            $unidades_N = $request->unidades_N ?? (($request->porcentaje_N ?? 0) / 100) * $request->kg_ha;
            $unidades_P = $request->unidades_P ?? (($request->porcentaje_P ?? 0) / 100) * $request->kg_ha;
            $unidades_K = $request->unidades_K ?? (($request->porcentaje_K ?? 0) / 100) * $request->kg_ha;
            $a = AplicacionFertilizante::create (
                [
                    'id_agricultor' => $id_agricultor,
                    'id_cultivo' => $request -> id_cultivo,
                    'nombre_fert' => $request-> nombre_fert ?? null,
                    'kg_ha' => $request -> kg_ha,
                    'km' => $request -> km,  //kilómetros de transporte
                    'unidades_N' => $unidades_N,
                    'unidades_P' => $unidades_P,
                    'unidades_K' => $unidades_K,
                ]
                );
                $this->sumarAplicaciones($request -> id_cultivo);
            return response()->json(['message'=>'Se ha añadido aplicación de fertilizante a cultivo', 'data'=>$a],200 );
        }catch (ValidationException $e) {
            // Capturamos la excepción de validación
            $errors = $e->validator->errors()->all(); // Obtener solo los mensajes de error como un array

            // Retornamos la respuesta en el formato deseado
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        }catch (Exception $e){
            LogController::errores('Se ha producido un error al añadir feritilizante'. $e->getMessage());
            return response()->json (['message'=>'No ha sido posible añadir fertilizante'], 500);
        }
    }

    public function sumarAplicaciones($idCultivo){
        $c = CultivoParcela::find ($idCultivo);
        
            $this->sincronizarResumenFertilizantes($c);
        
    }
    protected function sincronizarResumenFertilizantes(CultivoParcela $cultivoParcela)
    {
            $aplicaciones = $cultivoParcela->aplicacionesFertilizantes;

            if ($aplicaciones->isEmpty()) {
                // Si no hay aplicaciones, eliminamos el resumen si existía
                Fertilizante::where('id_cultivo_parcela', $cultivoParcela->id)->delete();
                return;
            }

            // Calculamos los totales
            $total_N = $aplicaciones->sum('unidades_N');
            $total_P = $aplicaciones->sum('unidades_P');
            $total_K = $aplicaciones->sum('unidades_K');
            $total_kg_ha = $aplicaciones->sum('kg_ha');
            $km_medio = $aplicaciones->sum('km');

            // Cálculo de porcentajes globales
            $porcentaje_N = $total_kg_ha > 0 ? ($total_N / $total_kg_ha) * 100 : null;
            $porcentaje_P = $total_kg_ha > 0 ? ($total_P / $total_kg_ha) * 100 : null;
            $porcentaje_K = $total_kg_ha > 0 ? ($total_K / $total_kg_ha) * 100 : null;

            // Guardamos o actualizamos en la tabla resumen
            Fertilizante::updateOrCreate(
                ['id_cultivo_parcela' => $cultivoParcela->id],
                [
                    'nombre_fertilizante' => 'Resumen automático',
                    'uds_N' => $total_N,
                    'uds_P' => $total_P,
                    'uds_K' => $total_K,
                    'porcentaje_N' => $porcentaje_N,
                    'porcentaje_P' => $porcentaje_P,
                    'porcentaje_K' => $porcentaje_K,
                    'kg_ha' => $total_kg_ha,
                    'km' => $km_medio,
                    'fecha' => now()
                ]
            );
    }
    /**
     * Añade las lecturas del sensor como aplicacion de fertilizante indicando el id_lecutra_sensor para saber que esa aplicación viene de la lectura de un sensor de nivel. 
     * El ususario deebe indicar manualmente que va a introducir esa lectura como aplicaicón en un cultivo concreto.
     */
    public function addLecturasSensor($id_agricultor) {
       
    }
    public function obtenerTanqueFertilizanteYLecturas($id_agricultor){
        
        try{
            if (isset($id_agricultor)){
               if (!auth()->user->esAdmin()){
                    return response()->json (["message" => "Error no tiene permisos para consultar estos datos del agricultor"], 422);
               }
            }else {
                $id_agricultor = auth()->user->id;
            }
            // $this->esAdminOPropietario($request);
            $parcelas = Parcela::where('id_agricultor', $id_agricultor)->pluck('id');

            $tanks = TanqueFertilizante::whereIn('id_parcela', $parcelas)->get();
            if ($tanks->isEmpty()) {
                return response()->json(['message' => 'No se han encontrado tanques de fertilizante para este agricultor.'], 404);
            }else{
                return response()->json(['message'=>'Ahí van los tanques', 'data'=>$tanks],200);
            }
       
        }catch (Exception $e){
            LogController::errores('Se ha producido un error al añadir feritilizante'. $e->getMessage());
            return response()->json (['message'=>'No ha sido obtener el tanque del fertilizante'], 500);
        }
    }
    public function obtenerAplicacionesAgricultor ($id_agricultor) {
        try {
            if (isset($id_agricultor)) {
                if (!auth()->user()->esAdmin()) {
                    return response()->json(["message" => "Error no tiene permisos para consultar estos datos del agricultor"], 422);
                }
            } else {
                $id_agricultor = auth()->user()->id;
            }
    
            $cultivosFertilizantes = CultivoParcela::with([
                'cultivo:*',
                'parcela:id,id_agricultor,nombre',
                'aplicacionesFertilizantes:id,id_cultivo,nombre_fert,id_agricultor,unidades_P,unidades_K,unidades_N,km,kg_ha',
                'tanqueFertilizante:*',
            ])->select('id', 'id_cultivo', 'id_parcela', 'fecha_siembra','fecha_recoleccion', 'dias_ciclo')
              ->whereHas('parcela', function($q4) use ($id_agricultor){
                  $q4->where('id_agricultor', $id_agricultor);
              })
              ->whereNull('fecha_baja')
              ->get();
    
            // Añadir lecturas a cada tanque de fertilizante asociado a cada cultivo
            foreach ($cultivosFertilizantes as $cultivo) {
                if ($cultivo->tanqueFertilizante && count($cultivo->tanqueFertilizante) > 0) {
                    foreach ($cultivo->tanqueFertilizante as $tanque) {
                        $tanque->lecturas = $tanque->lecturasSensor();
                    }
                }
            }
    
            return response()->json(['aplicaciones' => $cultivosFertilizantes], 200);
    
        } catch (Exception $e) {
            LogController::errores('Se ha producido un error al añadir fertilizante: ' . $e->getMessage());
            return response()->json(['message' => 'No ha sido posible obtener aplicaciones del agricultor'], 500);
        }
    }

  
    public function eliminarAplicacionFertilizante($id)
    {
        $aplicacion = AplicacionFertilizante::find($id);
        $this->sumarAplicaciones($aplicacion->id_cultivo);
        if (!$aplicacion) {
            return response()->json(['error' => 'Aplicación no encontrada.'], 404);
        }
        $aplicacion->delete();
        return response()->json(['message' => 'Aplicación eliminada correctamente.']);
    }
}
