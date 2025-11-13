<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agricultor;
use App\Models\Maquina;
use App\Models\Apero;
Use Exception;
class AgricultoresController extends Controller
{
    
     // Obtener todos los agricultores
     public function obtener()
     {
         $agricultores = Agricultor::all();
         return response()->json($agricultores, 200);
     }
 
     // Obtener agricultor por ID
     public function obtenerPorId($id)
     {
         $agricultor = Agricultor::find($id);
         if (!$agricultor) {
             return response()->json(['message' => 'Agricultor no encontrado'], 404);
         }
         return response()->json($agricultor, 200);
     }
 
     // Crear un nuevo agricultor
     public function crear(Request $request)
     {
         // Validar los datos de entrada
         $request->validate([
             'id_usuario' => 'required|exists:users,id',
             'telefono' => 'required|string|max:20',
             'referencia_junta' => 'nullable|string|max:255',
         ]);
 
         // Crear el nuevo agricultor
         $agricultor = Agricultor::create($request->all());
 
         return response()->json($agricultor, 201);
     }
 
     // Editar un agricultor existente
     public function editar(Request $request, $id)
     {
         $agricultor = Agricultor::find($id);
 
         if (!$agricultor) {
             return response()->json(['message' => 'Agricultor no encontrado'], 404);
         }
 
         // Validar los datos de entrada
         $request->validate([
             'telefono' => 'string|max:20',
             'referencia_junta' => 'string|max:255',
         ]);
 
         // Actualizar los datos del agricultor
         $agricultor->update($request->all());
 
         return response()->json(['message' => 'Agricultor actualizado con exito'], 200);
     }
 
     // Borrar un agricultor
     public function borrar($id)
     {
         $agricultor = Agricultor::find($id);

 
         if (!$agricultor) {
             return response()->json(['message' => 'Agricultor no encontrado'], 404);
         }
 
         $agricultor->delete();
 
         return response()->json(['message' => 'Agricultor eliminado con exito'], 200);
     }
     public function obtenerTodasLasMaquinas () {
        try{
            $ms = Maquina::all();
          return response () -> json (['status' => 200, 'mensaje' => 'Ahi van las maquinas disponibles', 'maquinas' => $ms]);
 
        }catch (Exception $e){
            return response () -> json (['status' => 403, 'mensaje' => $e->getMessage()]);
        }
     }
     public function obtenerTodasLosAperos () {
        try{
            $ms = Apero::all();
            return response () -> json (['status' => 200, 'mensaje' => 'Ahi van todos los aperos', 'aperos' => $ms]);
   
        }catch (Exception $e){
            return response () -> json (['status' => 403, 'mensaje' => $e->getMessage()]);
        }
     }
     public function agregarMaquina($idAgricultor, $idMaquina)
    {
        $agricultor = Agricultor::find($idAgricultor);
        $maquina = Maquina::find($idMaquina);

        if (!$agricultor || !$maquina) {
            return response()->json(['message' => 'Agricultor o maquina no encontrados'], 404);
        }

        // Agregar la maquina al agricultor
        $agricultor->maquinaria()->attach($idMaquina);

        return response()->json(['message' => 'Maquina agregada con exito al agricultor'], 200);
    }

    // Agregar un apero a un agricultor
    public function agregarApero($idAgricultor, $idApero)
    {
        $agricultor = Agricultor::find($idAgricultor);
        $apero = Apero::find($idApero);

        if (!$agricultor || !$apero) {
            return response()->json(['message' => 'Agricultor o apero no encontrados'], 404);
        }

        // Agregar el apero al agricultor
        $agricultor->aperos()->attach($idApero);

        return response()->json(['message' => 'Apero agregado con exito al agricultor'], 200);
    }

    // Eliminar una maquina de un agricultor
    public function eliminarMaquina($idAgricultor, $idMaquina)
    {
        $agricultor = Agricultor::find($idAgricultor);
        $maquina = Maquina::find($idMaquina);

        if (!$agricultor || !$maquina) {
            return response()->json(['message' => 'Agricultor o maquina no encontrados'], 404);
        }

        // Eliminar la maquina del agricultor
        $agricultor->maquinaria()->detach($idMaquina);

        return response()->json(['message' => 'Maquina eliminada con exito del agricultor'], 200);
    }

    // Eliminar un apero de un agricultor
    public function eliminarApero($idAgricultor, $idApero)
    {
        $agricultor = Agricultor::find($idAgricultor);
        $apero = Apero::find($idApero);

        if (!$agricultor || !$apero) {
            return response()->json(['message' => 'Agricultor o apero no encontrados'], 404);
        }

        // Eliminar el apero del agricultor
        $agricultor->aperos()->detach($idApero);

        return response()->json(['message' => 'Apero eliminado con exito del agricultor'], 200);
    }

    // Obtener todas las maquinas y aperos de un agricultor
    public function obtenerMaquinasyAperosAgricultor($idAgricultor)
    {
        $agricultor = Agricultor::with('maquinaria', 'aperos')->find($idAgricultor);
       
        if (!$agricultor) {
            return response()->json(['message' => 'Agricultor no encontrado'], 404);
        }

        return response()->json([
            'mensaje' => 'Ahí va el agricultor con sus cosas',
            'data' => $agricultor,
                ], 200);
    }

}
