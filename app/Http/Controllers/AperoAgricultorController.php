<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AperoAgricultor;
use App\Models\Apero;
use App\Models\User;
use Exception;

class AperoAgricultorController extends Controller
{
    // Listar aperos asociados a un agricultor
    public function index($idAgricultor)
    {
        $aperos = AperoAgricultor::with('apero')->where('id_agricultor', $idAgricultor)->get();
        return response()->json(['aperos' => $aperos], 200);
    }

    // Asociar un apero a un agricultor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_apero' => 'required|exists:aperos,id',
            'id_agricultor' => 'required|exists:users,id',
            // otros campos opcionales
        ]);
        try {
            $aperoAgricultor = AperoAgricultor::create($validated);
            return response()->json(['message' => 'Apero asociado correctamente', 'data' => $aperoAgricultor], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al asociar apero: ' . $e->getMessage()], 500);
        }
    }

    // Editar la asociación (por id de la tabla pivote)
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_apero' => 'sometimes|exists:aperos,id',
            'id_agricultor' => 'sometimes|exists:users,id',
            // otros campos opcionales
        ]);
        try {
            $aperoAgricultor = AperoAgricultor::findOrFail($id);
            $aperoAgricultor->update($validated);
            return response()->json(['message' => 'Asociación actualizada', 'data' => $aperoAgricultor], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    // Eliminar la asociación
    public function destroy($id)
    {
        try {
            $aperoAgricultor = AperoAgricultor::findOrFail($id);
            $aperoAgricultor->delete();
            return response()->json(['message' => 'Asociación eliminada'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }
}
