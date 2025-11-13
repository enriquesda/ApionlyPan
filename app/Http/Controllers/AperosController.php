<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Image\Facades\Image;
use App\Models\Apero;
use Exception;
use Illuminate\Support\Facades\Storage;

class AperosController extends Controller
{
    
    public function nuevoApero(Request $request)
    {
        $validator = $request->validate([
            'nombre'               => 'required|string|max:255',
            'labores_simapro'      => 'nullable|string',
            'tiempo_m_labor'       => 'nullable|numeric',
            'rendimiento_simapro'  => 'nullable|numeric',
            'unidad'               => 'nullable|string',
            'vida'                 => 'nullable|numeric',
            'peso'                 => 'nullable|numeric',
            'consumo'              => 'nullable|numeric',
        ], [
            'nombre.required'           => 'El campo "nombre" es obligatorio.',
            'nombre.string'             => 'El campo "nombre" debe ser una cadena de texto.',
            'nombre.max'                => 'El campo "nombre" no puede tener más de 255 caracteres.',

            'labores_simapro.string'    => 'El campo "labores Simapro" debe ser una cadena de texto.',

            'tiempo_m_labor.numeric'       => 'El campo "tiempo medio labor" debe ser un número.',
            'rendimiento_simapro.numeric'  => 'El campo "rendimiento Simapro" debe ser un número.',
            'vida.numeric'                 => 'El campo "vida" debe ser un número.',
            'peso.numeric'                 => 'El campo "peso" debe ser un número.',
            'consumo.numeric'              => 'El campo "consumo" debe ser un número.',
        ]);

        try {
            // Cálculos opcionales si no se envían
            if (!isset($validator['consumo'])) {
                $validator['consumo'] = ($validator['peso'] ?? 1000) * ($validator['rendimiento_simapro'] ?? 0) / ($validator['vida'] ?? 1);
            }

            $apero = Apero::create($validator);

            return response()->json([
                'message' => 'Apero creado con éxito',
                'data'    => $apero
            ], 201);

        } catch (Exception $e) {
            // Puedes usar tu LogController aquí si lo tienes configurado
            // LogController::errores("No ha sido posible crear apero: ". $e->getMessage());
            return response()->json(['message' => 'Error al crear el apero: ' . $e->getMessage()], 500);
        }
    }
    public function obtenerTodos()
    {
        try {
            $aperos = Apero::all();

            return response()->json(['data' => $aperos], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al obtener los aperos: ' . $e->getMessage()], 500);
        }
    }

    public function editarApero(Request $request, $id)
    {
        try {
            $validator = $request->validate([
                'nombre'               => 'sometimes|required|string|max:255',
                'labores_simapro'      => 'nullable|string',
                'tiempo_m_labor'       => 'nullable|numeric',
                'rendimiento_simapro'  => 'nullable|numeric',
                'unidad'               => 'nullable|string',
                'vida'                 => 'nullable|numeric',
                'peso'                 => 'nullable|numeric',
                'consumo'              => 'nullable|numeric',
            ], [
                'nombre.required'           => 'El campo "nombre" es obligatorio si se envía.',
                'nombre.string'             => 'El campo "nombre" debe ser una cadena de texto.',
                'nombre.max'                => 'El campo "nombre" no puede tener más de 255 caracteres.',
                'labores_simapro.string'    => 'El campo "labores Simapro" debe ser una cadena de texto.',
                'tiempo_m_labor.numeric'       => 'El campo "tiempo medio labor" debe ser un número.',
                'rendimiento_simapro.numeric'  => 'El campo "rendimiento Simapro" debe ser un número.',
                'vida.numeric'                 => 'El campo "vida" debe ser un número.',
                'peso.numeric'                 => 'El campo "peso" debe ser un número.',
                'consumo.numeric'              => 'El campo "consumo" debe ser un número.',
            ]);

            $apero = Apero::findOrFail($id);

            // Si no se manda consumo, recalcularlo
            if (!isset($validator['consumo'])) {
                $validator['consumo'] = ($validator['peso'] ?? $apero->peso ?? 1000) * ($validator['rendimiento_simapro'] ?? $apero->rendimiento_simapro ?? 0)/ ($validator['vida'] ?? $apero->vida ?? 1);
            }

            $apero->update($validator);

            return response()->json([
                'message' => 'Apero actualizado con éxito',
                'data'    => $apero
            ], 200);

        } catch (ValidationException $ve) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $ve->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al editar el apero: ' . $e->getMessage()], 500);
        }
    }

 
    public function borrarApero($id)
    {
        try {
            $apero = Apero::findOrFail($id);
            $apero->delete();

            return response()->json(['message' => 'Apero eliminado con éxito'], 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Error al eliminar el apero: ' . $e->getMessage()], 500);
        }
    }

    public function subirImagen(Request $request, $id)
    {
        try {
            // Validar que el archivo sea imagen y no supere 2MB
            $request->validate([
                'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $file = $request->file('imagen');
            $apero = Apero::find($id);
            if (!$apero) {
                LogController::errores('Intento de subir imagen a apero no encontrado con ID: ' . $id);
                return response()->json(['message' => 'Apero no encontrado'], 404);
            }
            // Crear nombre de archivo: apero_23_timestampdeGuardado.jpg
            $nombreArchivo = 'apero_' . $id . '_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();

            // Eliminar imagen anterior si existe
            if ($apero->img && Storage::disk('public')->exists('aperos/' . $apero->img)) {
                Storage::disk('public')->delete('aperos/' . $apero->img);
            }
            $apero->img = $nombreArchivo;
            $apero->save();
            Storage::disk('public')->putFileAs('aperos', $file, $nombreArchivo);

            return response()->json([
                'mensaje' => 'Imagen subida correctamente',
                'ruta' => $nombreArchivo
            ], 200);
            
        } catch (\Exception $e) {
            LogController::errores('Error al subir imagen de apero: ' . $e->getMessage());
            return response()->json([
                'mensaje' => 'Error al procesar la imagen',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
