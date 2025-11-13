<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maquina;
use Illuminate\Validation\ValidationException;
use Illuminate\Image\Facades\Image;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Models\MaquinaAgricultor;



class MaquinasController extends Controller
{
    //
    public function obtenerMaquinaria()
    {
        $maquinas = Maquina::select([
            'id',
            'tipo_cultivo',
            'nombre',
            'cv',
            'kw',
            'consumo_l_h',
            'rendimiento_h_ha',
            'vida_h',
            'peso',
            'img',
        ])->get();
        return response()->json(['data'=>$maquinas], 200);
    }
    public function nuevaMaquina(Request $request){
        $validator = $request->validate([
            'tipo_cultivo'     => 'required|string',
            'nombre'           => 'required|string|max:255',
            'cv'               => 'nullable|numeric',
            'kw'               => 'nullable|numeric',
            'consumo_l_h'      => 'nullable|numeric',
            'rendimiento_h_ha' => 'nullable|numeric',
            'consumo_l_ha'     => 'nullable|numeric',
            'vida_h'           => 'nullable|numeric',
            'peso'             => 'nullable|numeric',
            'fabricacion'      => 'nullable|numeric',
            'reparacion'       => 'nullable|numeric',
            // 'img'              => 'nullable|image|max:2048' // máximo 2MB
        ],[
            'tipo_cultivo.required' => 'El campo "tipo de cultivo" es obligatorio.',
            'tipo_cultivo.string'   => 'El campo "tipo de cultivo" debe ser una cadena de texto.',
        
            'nombre.required'       => 'El campo "nombre" es obligatorio.',
            'nombre.string'         => 'El campo "nombre" debe ser una cadena de texto.',
            'nombre.max'            => 'El campo "nombre" no puede tener más de 255 caracteres.',
        
            'cv.numeric'            => 'El campo CV debe ser un valor numérico.',
            'kw.numeric'            => 'El campo kW debe ser un valor numérico.',
        
            'consumo_l_h.numeric'      => 'El campo "consumo L/h" debe ser un valor numérico.',
            'rendimiento_h_ha.numeric' => 'El campo "rendimiento h/ha" debe ser un valor numérico.',
            'consumo_h_ha.numeric'     => 'El campo "consumo h/ha" debe ser un valor numérico.',
            'vida_h.numeric'           => 'El campo "vida en horas" debe ser un valor numérico.',
            'peso.numeric'             => 'El campo "peso" debe ser un valor numérico.',
        
            'fabricacion.number'    => 'El campo "fabricación" debe ser una cadena de texto.',
            'reparacion.number'     => 'El campo "reparación" debe ser una cadena de texto.',
        
            // 'img.image'             => 'El archivo debe ser una imagen válida.',
            // 'img.max'               => 'El archivo de imagen no debe superar los 2MB.',
        ]);

       

    
        try{
            // // Si se envía imagen, se almacena en el disco 'public/maquinas'
            // if ($request->hasFile('img')) {
            //     $path = $request->file('img')->store('maquinas', 'public');
            //     $validator['img'] = $path; //almacenamos el path de la imagen
            // }
          // Cálculo del consumo_l_ha: si alguno de los dos valores no está, asumimos 1
            // if (!isset($validator->rendimiento_h_ha)){
            //     $validator['rendimiento_h_ha'] = 
            // }
            if (!isset($validator['fabricacion'])){
                $validator['fabricacion'] = ($validator['peso'] ?? 1000) * ($validator['rendimiento_h_ha'] ?? 0)/ ($validator['vida_h'] ?? 1);
            }
          
            if (!isset($validator['reparacion'])){
                $validator['reparacion'] = $validator['fabricacion'] * 0.2;
            }
            
            // Sobrescribimos o añadimos al array de datos validados
            if (!isset ($validator['consumo_l_ha'])){
                $consumo_l_ha = ($validator['consumo_l_h'] ?? 1) * ($validator['rendimiento_h_ha'] ?? 0);
                $validator['consumo_l_ha'] = $consumo_l_ha;
            }

            // Crear la máquina con todos los datos, incluido el cálculo
            $maquina = Maquina::create($validator);
            return response()->json([
                'message' => 'Máquina creada con éxito',
                'data'    => $maquina
            ], 201);
        }catch (Exception $e){
            
            LogController::errores("No ha sido posible crear maquinaria ". $e->getMessage());
            return response()->json (['message' => 'Error al crear la maquina: '. $e->getMessage()], 500);
        }
    }
    public function editarMaquina(Request $request){
        try{
                $validator = $request->validate([
                    'id'               => 'required|numeric',
                    'tipo_cultivo'     => 'sometimes|required|string',
                    'nombre'           => 'sometimes|required|string|max:255',
                    'cv'               => 'sometimes|nullable|numeric',
                    'kw'               => 'sometimes|nullable|numeric',
                    'consumo_l_h'      => 'sometimes|nullable|numeric',
                    'rendimiento_h_ha' => 'sometimes|nullable|numeric',
                    'consumo_l_ha'     => 'sometimes|nullable|numeric',
                    'vida_h'           => 'sometimes|nullable|numeric',
                    'peso'             => 'sometimes|nullable|numeric',
                    'fabricacion'      => 'sometimes|nullable|string',
                    'reparacion'       => 'sometimes|nullable|string',
                ]);
        
                $maquina = Maquina::findOrFail($request['id']);
                $maquina->update($validator);
        
                return response()->json([
                    'message' => 'Máquina actualizada con éxito',
                    'data'    => $maquina
                ], 200);
         
        }catch (ValidationException $e) {
            // Capturamos la excepción de validación
           
            $errors = $e->validator->errors()->all(); // Obtener solo los mensajes de error como un array

            // Retornamos la respuesta en el formato deseado
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        }catch (Exception $e){
            
            LogController::errores("No ha sido posible editar maquinaria ". $e->getMessage());
            return response()->json (['message' => 'Error al editar la maquina contacte con el desarrollador'], 500);
        } 
    }
    public function eliminarMaquina($id){
        try{
            $maquina = Maquina::findOrFail($id);

            // Elimina la imagen asociada, de existir
            if ($maquina->img && Storage::disk('public/maquinas')->exists($maquina->img)) {
                Storage::disk('public/maquinas')->delete($maquina->img);
            }

            $maquina->delete();

            return response()->json(['message' => 'Máquina eliminada con éxito'], 200);
        }catch (Exception $e){
            LogController::errores("No se ha podido editar la maquina ". $e->getMessage());
            return response()->json (['message' => 'Error al editar la maquina contacte con el desarrollador'], 500);
        }
    }
    public function subirImagen(Request $request, $id)
    {
        // Validar que el archivo sea imagen y no supere 2MB
        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    
        $file = $request->file('imagen');
        $m = Maquina::find ($id);
        // Crear nombre de archivo: maquinaria_23_timestampdeGuardado.jpg
        $nombreArchivo = 'maquinaria_' . $id .'_'.now(). '.' . $file->getClientOriginalExtension();
        //  Maquina::find($id)->update(['img' => $nombreArchivo]);
        
        // Carpeta destino: public/maquinaria/
        // $ruta = public_path('maquinaria');
        
        // // Crear carpeta si no existe
        // if (!file_exists($ruta)) {
        //     mkdir($ruta, 0755, true);
        // }
        
        if ($m->img && Storage::disk('public')->exists('maquinas/' . $m->img)) {
           
            Storage::disk('public')->delete('maquinas/' . $m->img);
        }
        $m->img = $nombreArchivo;
        $m->save();
        Storage::disk('public')->putFileAs('maquinas', $file, $nombreArchivo); //intelephese da error en esta linea pero la función putFileAs existe (habría que preguntar a chatGPT como bypasear esto)
        // Mover el archivo al destino
        // $file->move($ruta, $nombreArchivo);
    
        // // Ruta pública relativa para guardar o mostrar
        // $rutaPublica = 'maquinaria/' . $nombreArchivo;
    
        return response()->json([
            'mensaje' => 'Imagen subida correctamente',
            'ruta' => $nombreArchivo
        ], 200);
    }

    public function esAdminOPropietario($obj){
    
        if (auth()->user()->esPropietarioDe($obj)==true){
            return true;    
           }else{
             abort (403, "Parece que no tiene permisos para acceder a este recurso");
           }
    }
    public function obtenerMaquinariaAgricultor($idAgricultor)
    {
        try {
        
            if (isset($idAgricultor)){
                $this->esAdminOPropietario(['id_agricultor'=>$idAgricultor]);
            }else {
                $idAgricultor = auth()->user()->id;
            }

            $maquinas = MaquinaAgricultor::with('maquina')
                ->where('id_agricultor', $idAgricultor)
                ->get();

            return response()->json(['maquinaria' => $maquinas], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al obtener la maquinaria', 'error' => $e->getMessage()], 500);
        }
    }

    public function crearMaquinaAgricultor (Request $request){
        try {
            $data = $request->validate([
                'id_agricultor' => 'required|exists:users,id',
                'id_maquina'    => 'required|exists:maquinaria,id',
                'id_sensor'     => 'nullable|numeric',
                'device'        => 'nullable|string|max:255',
            ]);

            $maquina = MaquinaAgricultor::create($data);

            return response()->json(['message' => 'Máquina asociada correctamente', 'data' => $maquina], 201);
        } catch (ValidationException $e) {
            LogController::errores("Errores de validacion ".$e->validator->errors());
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            LogController::errores("Error del servidor ".$e->getMessage());
            return response()->json(['message' => 'Error al crear la asociación', 'error' => $e->getMessage()], 500);
        }
    }

    
    public function editarMaquinaAgricultor (Request $request) {
        try {
            $data = $request->validate([
                'id'        =>  'required',
                'id_maquina' => 'nullable|exists:maquinaria,id',
                'id_sensor'  => 'nullable|numeric',
                'device'     => 'nullable|string|max:255',
            ]);
    
            $maquina = MaquinaAgricultor::findOrFail($data['id']);
            $maquina->update($data);
    
            return response()->json(['message' => 'Máquina actualizada con éxito', 'data' => $maquina], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar la máquina', 'error' => $e->getMessage()], 500);
        }
    }
    public function eliminarMaquinaAgricultor($id){
        try {
            $maquina = MaquinaAgricultor::findOrFail($id);
            $maquina->delete();
    
            return response()->json(['message' => 'Máquina eliminada correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al eliminar la máquina', 'error' => $e->getMessage()], 500);
        }
    }
    
}
