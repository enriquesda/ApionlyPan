<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fitosanitario;
use App\Models\User;
use App\Models\CultivoParcela;
use App\Models\FitosanitarioCultivo;
use Illuminate\Support\Facades\Validator;
use Exception;
class FitosanitariosController extends Controller
{
    /**
     * Devuelve todas las características
     */
    public function obtenerTiposFitosanitarios(){
        $f= Fitosanitario::all();
       
        return response()->json($f, 200);
    }
    public function esAdminOPropietario($obj){
    
        if (auth()->user()->esPropietarioDe($obj)==true){
            return true;    
           }else{
             abort (403, "Parece que no tiene permisos para acceder a este recurso");
           }
    }

    /**
     * Pretende obtener las aplicaciones de fitosanitarios de un agricultor para ello hay que comprobar si el agricultor es el dueño o lo 
     * está trantando de obtener un administrador de otro modo no será posible.
     */
    public function obtnerAplicacionesDeAgricultor ( $agricultorId){
        //Esta forma es válida, busca las aplicaciones de fitosanitarios en sentido contrario, es decir, hacia atras desde aplicaciones de fitosanitarios
        //hasta el cultivo pero falta información del cultivo para listarlos adecuadamente
        // if (is_int($agricultorId) && $agricultorId>0){ //esta comprobación es un poco absurda ya que dudo que a traves de la interfaz de usuario se vayan a realizar peticiones con un id de usuario no valido ya que se obtienen de la bd
        //     $aplicaciones = FitosanitarioCultivo::whereHas('cultivoParcela', function ($q) use ($agricultorId) {
        //         $q->whereHas('parcela', function ($q2) use ($agricultorId) {
        //             $q2->where('id_agricultor', $agricultorId);
        //         });
        //     })->with('fitosanitario')->get();
        //     return response()->json (['apliciones'=> $aplicaciones], 200);
        // }else{
        //     return response()->json (['error'=>'Id de agricultor no válido'], 500);
        // }

        try{
            $this->esAdminOPropietario(['id_agricultor'=>$agricultorId]);
         
                $cultivosFitosanitarios = CultivoParcela::with([
                    'cultivo:id,nombre',
                    'parcela:id,id_agricultor,nombre',
                    'fitosanitarios:id,nombre,materia_activa,tipo,fitosanitarios_cultivo.n_aplicaciones,fitosanitarios_cultivo.fecha',
                ])->select('id', 'id_cultivo', 'id_parcela', 'fecha_siembra','fecha_recoleccion', 'dias_ciclo')->whereHas('parcela', function($q4) use ($agricultorId){
                    $q4 -> where ('id_agricultor', $agricultorId);
                })->whereNull('fecha_baja')->get();
                
                return response()->json (['aplicaciones'=> $cultivosFitosanitarios], 200);
          
        }catch (Exception $e){
           
            LogController::errores("Ha habido un error obteniendo los fitosanitarios del agricultor: ". $e->getMessage());
            return response()->json (['error'=>"Error al obtener los fitosanitarios del agricultor "], 500);
        }
        
    } 
    /**
     * Para añadir muchas aplicaciones al mismno tiempo de distitos fitosanitarios sobre un mismo cultivo. (Aunque ya existe una implementación parecida para el cuestionario general que
     * se implementó para el T-Riegos )
     */
    public function addAplicaciones(Request $request){
         // Validación de los datos de entrada con mensajes de error personalizados

         $validator = Validator::make($request->all(), [
            'id_cultivo'      => 'required|exists:cultivo_parcela,id',
            'fitosanitarios'  => 'array'
        ], [
            'id_cultivo.required'     => 'El id del cultivo es obligatorio.',
            'id_cultivo.exists'       => 'El cultivo especificado no existe.',
            'fitosanitarios.required' => 'El campo fitosanitarios es obligatorio.',
            'fitosanitarios.array'    => 'El campo fitosanitarios debe ser un array válido.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        try{
        // Se busca el cultivo por su ID
        $cultivo = CultivoParcela::select('id')->with('fitosanitarios')->find($request->input('id_cultivo'));
        $this->esAdminOPropietario($cultivo);
        //si el cultivo está finalizado y se ha calculado ya el informe impacto de simapro no se deben poder editar aplicaciones de fitosanitarios sin que un administrador no lo sepa.
        if ($cultivo->tiene_informe_impacto && !auth()->user()->esAdmin()){ 
            return response()->json(['messasge' => 'Para editar las aplicaciones de fitosanitarios una vez se haya calculado el informe impacto debe hacerlo un administrador'], 403);
        }
        if (!$cultivo) {
            return response()->json([
                'error' => 'Cultivo no encontrado.'
            ], 404);
        }

        // Decodificar el JSON recibido en un arreglo asociativo
        $fitosanitariosArray = $request->input('fitosanitarios');

        if (!is_array($fitosanitariosArray)) {
            return response()->json([
                'error' => 'El campo fitosanitarios debe ser un arreglo válido.'
            ], 422);
        }

        $syncData = [];
        $counter = 0;
        foreach ($fitosanitariosArray as $index => $item) {
            if (!isset($item['id']) || !isset($item['n_aplicaciones'])) {
                return response()->json([
                    'error' => "El elemento en la posición {$index} debe tener definidos 'id' y 'n_aplicaciones'."
                ], 422);
            }

            $syncData[$counter]=([
                'id_fitosanitario' => $item['id'],
                'n_aplicaciones' => $item['n_aplicaciones'],
                'fecha' => isset($item['fecha']) ? substr($item ['fecha'],0,10) : now()
            ]
        );
        $counter++;
        }


        $cultivo->fitosanitarios()->sync($syncData);
        // if (){
        //     echo("Parece se han guardado bien");
        // }else{
        //     echo ("Fitosanitarios no sincronizados");
        // }
       
            return response()->json([
                'message' => 'Aplicaciones de fitosanitarios actualizadas correctamente.'
            ], 200);
        }catch (Exception $e){
            LogController::errores ("Error al sincronizar los fitosanitarios del cultivo ".$e->getMessage());
            return response()->json (['error'=>'Error al sincronizar los fitosanitarios del cultivo '.$e->getMessage()],500);
        }
    }
    public function addAplicacion (Request $request){
        $validator = Validator::make($request->all(), [
            'id_cultivo'      => 'required|exists:cultivo_parcela,id',
            'fitosanitarios'  => 'required|json'
        ], [
            'id_cultivo.required'     => 'El id del cultivo es obligatorio.',
            'id_cultivo.exists'       => 'El cultivo especificado no existe.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $idFitosanitario = $request['fitosanitario']['id'];//no se si será esta la estructura en la que vendrá la aplicación del fitosanitario
        $cultivo = CultivoParcela::find($request['id_cultivo']);
        $yaExiste = $cultivo->fitosanitarios()->where('id_fitosanitario', $idFitosanitario)->exists();

        if ($yaExiste) {
            $cultivo->fitosanitarios()->updateExistingPivot($idFitosanitario, [
                'n_aplicaciones' => $request['n_aplicaciones'],
                'fecha' => $request['fecha'] ?? now(),
            ]);
        } else {
            $cultivo->fitosanitarios()->attach($idFitosanitario, [
                'n_aplicaciones' => $request['n_aplicaciones'],
                'fecha' =>  $request['fecha'] ?? now(),
            ]);
        }

    }
    /**
     *
     */
    public function eliminarAplicacion (Request $request){
        try {
            $idCultivoParcela = $request['id_cultivo'];
            $idFitosanitario = $request['id_fitosanitario']; //en realidad no es el id del fitosanitario si on de la aplicación del fitosanitario que identifica dicha acción en la tabla correspondiente (fitosanitarios_cultivo)
            $cultivo = CultivoParcela::findOrFail($idCultivoParcela);
            //si el cultivo está finalizado y se ha calculado ya el informe impacto de simapro no se deben poder editar aplicaciones de fitosanitarios sin que un administrador no lo sepa.
            if ($cultivo->getTieneInformeImpacto() && auth()->user()->rol > 2){ 
                return response()->json(['messasge' => 'Para editar las aplicaciones de fitosanitarios una vez se haya calculado el informe impacto debe hacerlo un administrador'], 403);
            }
            if ($cultivo->fecha)
                $cultivo->fitosanitarios()->detach($idFitosanitario);
    
            return response()->json(['message' => 'Aplicación de fitosanitario eliminada correctamente.']);
        } catch (Exception $e) {
            LogController::errores ("Error al eliminar aplicación de fitosanitario ".$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
    public function nuevoFitosanitario(Request $request)
    {
        // Validamos los datos recibidos
        $data = $request->validate([
            'nombre'                  => 'required|string|max:255',
            'materia_activa'          => 'required|string',
            'clasificacion_simapro'   => 'required|string',
            'porcentaje_ma'           => 'required|numeric',
            'densidad'                => 'required|numeric',
            'tipo'                    => 'required|integer', 
            'presion_vapor'           => 'nullable|numeric',
            'max_tomate_L_ha'         => 'nullable|numeric',
            'max_tomate_ha'           => 'nullable|numeric',
            'max_arroz_L_ha'          => 'nullable|numeric',
            'max_arroz_ha'            => 'nullable|numeric',
            'max_frutal_L_ha'         => 'nullable|numeric',
            'max_frutal_ha'           => 'nullable|numeric',
            'max_vid_L_ha'            => 'nullable|numeric',
            'max_ha_vid'              => 'nullable|numeric',
            'max_olivo_L_ha'          => 'nullable|numeric',
            'max_olivo_ha'            => 'nullable|numeric',
        ]);

        // Se crea el registro
        $fitosanitario = Fitosanitario::create($data);

        return response()->json([
            'message' => 'Fitosanitario creado correctamente',
            'data'    => $fitosanitario
        ], 201);
    }

    /**
     * Edita un registro existente en la tabla 'fitosanitarios'
     */
    public function editarFitosanitario(Request $request)
    {
        // Validamos que se envíe un ID existente y los demás campos requeridos
        $data = $request->validate([
            'id'                      => 'required|exists:fitosanitarios,id',
            'nombre'                  => 'required|string|max:255',
            'materia_activa'          => 'required|string',
            'clasificacion_simapro'   => 'required|string',
            'porcentaje_ma'           => 'required|numeric',
            'densidad'                => 'required|numeric',
            'tipo'                    => 'required|integer',
            'presion_vapor'           => 'nullable|numeric',
            'max_tomate_L_ha'         => 'nullable|numeric',
            'max_tomate_ha'           => 'nullable|numeric',
            'max_arroz_L_ha'          => 'nullable|numeric',
            'max_arroz_ha'            => 'nullable|numeric',
            'max_frutal_L_ha'         => 'nullable|numeric',
            'max_frutal_ha'           => 'nullable|numeric',
            'max_vid_L_ha'            => 'nullable|numeric',
            'max_ha_vid'              => 'nullable|numeric',
            'max_olivo_L_ha'          => 'nullable|numeric',
            'max_olivo_ha'            => 'nullable|numeric',
        ],[
            'id.required'                      => 'El campo id es obligatorio.',
            'id.exists'                        => 'El id proporcionado no existe en fitosanitarios.',
        
            'nombre.required'                  => 'El campo nombre es obligatorio.',
            'nombre.string'                    => 'El campo nombre debe ser una cadena de texto.',
            'nombre.max'                       => 'El campo nombre no puede tener más de 255 caracteres.',
        
            'materia_activa.required'          => 'El campo materia activa es obligatorio.',
            'materia_activa.string'            => 'El campo materia activa debe ser una cadena de texto.',
        
            'clasificacion_simapro.required'   => 'El campo clasificación SIMAPRO es obligatorio.',
            'clasificacion_simapro.string'     => 'El campo clasificación SIMAPRO debe ser una cadena de texto.',
        
            'porcentaje_ma.required'           => 'El campo porcentaje materia activa es obligatorio.',
            'porcentaje_ma.numeric'            => 'El campo porcentaje materia activa debe ser un valor numérico.',
        
            'densidad.required'                => 'El campo densidad es obligatorio.',
            'densidad.numeric'                 => 'El campo densidad debe ser un valor numérico.',
        
            'tipo.required'                    => 'El campo tipo es obligatorio.',
            'tipo.integer'                     => 'El campo tipo debe ser un número entero.',
        
            'presion_vapor.numeric'            => 'El campo presión de vapor debe ser un valor numérico.',
        
            'max_tomate_L_ha.numeric'          => 'El campo max tomate L/ha debe ser un valor numérico.',
            'max_tomate_ha.numeric'            => 'El campo max tomate ha debe ser un valor numérico.',
        
            'max_arroz_L_ha.numeric'           => 'El campo max arroz L/ha debe ser un valor numérico.',
            'max_arroz_ha.numeric'             => 'El campo max arroz ha debe ser un valor numérico.',
        
            'max_frutal_L_ha.numeric'          => 'El campo max frutal L/ha debe ser un valor numérico.',
            'max_frutal_ha.numeric'            => 'El campo max frutal ha debe ser un valor numérico.',
        
            'max_vid_L_ha.numeric'             => 'El campo max vid L/ha debe ser un valor numérico.',
            'max_ha_vid.numeric'               => 'El campo max ha vid debe ser un valor numérico.',
        
            'max_olivo_L_ha.numeric'           => 'El campo max olivo L/ha debe ser un valor numérico.',
            'max_olivo_ha.numeric'             => 'El campo max olivo ha debe ser un valor numérico.',
        ]);

        // Se busca el registro a actualizar
        $fitosanitario = Fitosanitario::findOrFail($data['id']);
        try{
        // Se actualiza el registro con los nuevos datos
        $fitosanitario->update($data);
        
        return response()->json([
            'message' => 'Fitosanitario actualizado correctamente',
            'data'    => $fitosanitario
        ], 200);
        }catch (Exception $e){
            LogController::errores ('Error de edición de fitosanitario'.$e->getMessage());
            return response ()->json (['message'=>'No se ha podido editar el fitosantario, contacte con el desarrollador'],500);
        }
    }

    /**
     * Elimina un registro de la tabla 'fitosanitarios'
     */
    public function eliminarFitosanitario($id)
    {
        // Validamos que se envíe un ID existente
        // $data = $request->validate([
        //     'id' => 'required|exists:fitosanitarios,id',
        // ]);
        try {

        
        // Se busca el registro a eliminar
        $fitosanitario = Fitosanitario::findOrFail($id);

        // Se elimina el registro
        $fitosanitario->delete();

        return response()->json([
            'message' => 'Fitosanitario eliminado correctamente'
        ], 200);
        }catch (Exception $e){
            LogController::errores ("Eliminar fitosanitario de la bd ".$e->getMessage());
        }
    }
}
