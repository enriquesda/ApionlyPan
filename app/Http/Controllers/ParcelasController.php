<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Models\Filtro;
use App\Models\TanqueFertilizante;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

use Exception;

class ParcelasController extends Controller
{
    public function esAdminOPropietario($obj)
    {

        if (auth()->user()->esPropietarioDe($obj) == true) {
            return true;
        } else {
            abort(403, "Parece que no tiene permisos para acceder a este recurso");
        }
    }
    /**
     * Obtiene todas las parcelas habidas y por haber en la App.
     */
    public function obtenerTodas()
    {
        $parcelas = Parcela::all();
        return response()->json($parcelas, 200);
    }

    /**
     * Obtiene todas las parcelas de un agricultor.
     */
    public function obtenerParcelasAgricultor($idAgricultor)
    {
        // $parcelas = Parcela::with(['filtros', 'cultivos'])->where('id_agricultor', $idAgricultor)->get();
        $this->esAdminOPropietario(['id_agricultor' => $idAgricultor]);
        $parcelas = Parcela::with(['filtros'])->where('id_agricultor', $idAgricultor)->whereNull('fecha_baja')->get();
        if ($parcelas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron parcelas para el agricultor'], 404);
        }

        return response()->json($parcelas, 200);
    }

    /**
     * Crear una nueva parcela.
     */
    public function crearParcela(Request $request)
    {
        try {
            $this->esAdminOPropietario($request);
            $request->validate([
                'id_agricultor' => 'required|exists:users,id',
                'nombre' => 'required|string',
                'municipio' => 'nullable|numeric',
                'provincia' => 'nullable|numeric',
                'numero_sigpac' => 'nullable|string|unique:parcelas,numero_sigpac',
                'superficie' => 'nullable|numeric',
                'poligono' => 'nullable|numeric',
                'parcela' => [
                    'nullable',
                    'numeric',
                    Rule::unique('parcelas')->where(function ($query) use ($request) {
                        return $query->where(['poligono' => $request->input('poligono'), 'provincia' => $request->input('provincia'), 'municipio' => $request->input('municipio')]);
                    }),
                ],
                'zona' => 'nullable|numeric',
                'agregado' => 'nullable|numeric',
                'recinto' => 'nullable|numeric',
                'ancho_caseta' => 'nullable|numeric',
                'largo_caseta' => 'nullable|numeric',
                'alto_caseta' => 'nullable|numeric',
                'id_parcela_pp' => 'nullable',
                'pais' => 'required|string',
                'concelho' => 'nullable|string',
                'distrito' => 'nullable|string',

            ]);

            // Crear la nueva parcela
            $parcela = Parcela::create($request->all());

            return response()->json($parcela, 201);
        } catch (ValidationException $e) {
            // Capturamos la excepción de validación
            $errors = $e->validator->errors()->all(); // Obtener solo los mensajes de error como un array

            // Retornamos la respuesta en el formato deseado
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }

    /**
     * Esta función recibe en la request un array con los ids de los filtros que se quieren asociar al idParcela
     * por ejemplo:
     * { "filtros": [1, 4, 2]}
     */
    public function agregarFiltrosParcela(Request $request, $idParcela)
    {
        try {

            $parcela = Parcela::findOrFail($idParcela);
            $this->esAdminOPropietario($parcela);
            // Validar los datos de entrada
            // $request->validate([
            //     'filtros' => 'required|array',
            //     'filtros.*' => 'exists:filtros,id'  // Cada filtro debe existir en la tabla filtros
            // ]);
            //Esto no necesito comprobarlo ya que la request va a venir prefabricada correctamenete
            //con las restricciones del formulario del front-end

            // Agregar los filtros a la parcela
            //$parcela->filtros()->syncWithoutDetaching($request->filtros);
            $parcela->filtros()->sync($request->filtros); //la función sync elimina los filtros relacionados con la parcela que no aparezcan en el array si los hubiera
            return response()->json(['message' => 'Filtros agregados con exito a la parcela'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }

    /**
     * Esta función recibe en la request un array con los ids de los filtros que se quieren eliminar de la idParcela
     * por ejemplo:
     * { "filtros": [1, 4, 2]}
     */
    public function actualizarFiltrosParcela(Request $request, $idParcela)
    {
        try {
            $parcela = Parcela::findOrFail($idParcela);
            $this->esAdminOPropietario($parcela);
            // Validar los datos de entrada
            $request->validate([
                'filtros' => 'nullable|array',             // Puede ser un array vacío
                'filtros.*' => 'exists:filtros,id'         // Cada filtro debe existir en la tabla filtros
            ]);

            // Eliminar todos los filtros asociados a la parcela
            $parcela->filtros()->detach();

            // Agregar los nuevos filtros si se proporcionan en la solicitud
            if (!empty($request->filtros)) {
                $parcela->filtros()->attach($request->filtros);
            }

            return response()->json(['message' => 'Filtros actualizados con éxito para la parcela'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }

    /**
     * Editar una parcela existente.
     */
    public function editar(Request $request)
    {
        try {
            $parcela = Parcela::find($request['id']);
            $this->esAdminOPropietario($parcela);

            if (!$parcela) {
                return response()->json(['message' => 'Parcela no encontrada'], 404);
            }

            // Validación según el país
            if ($request->has('pais') && $request->input('pais') == 'portugal') {
                $request->validate([
                    'nombre' => 'required|string',
                    'superficie' => 'nullable|numeric',
                    'ancho_caseta' => 'nullable|numeric',
                    'largo_caseta' => 'nullable|numeric',
                    'alto_caseta' => 'nullable|numeric',
                    'id_parcela_pp' => 'nullable',
                    'pais' => 'required|string',
                    'concelho' => 'required|string',
                    'distrito' => 'required|string',
                ]);
            } else {
                $request->validate([
                    'nombre' => 'required|string',
                    'municipio' => 'required|numeric',
                    'provincia' => 'required|numeric',
                    'numero_sigpac' => 'nullable|string|unique:parcelas,numero_sigpac,' . $request->id,
                    'superficie' => 'nullable|numeric',
                    'poligono' => 'required|numeric',
                    'parcela' => [
                        'required',
                        'numeric',
                        Rule::unique('parcelas')->ignore($request->id)->where(function ($query) use ($request) {
                            return $query->where([
                                'poligono' => $request->input('poligono'),
                                'provincia' => $request->input('provincia'),
                                'municipio' => $request->input('municipio'),
                            ]);
                        }),
                    ],
                    'zona' => 'nullable|numeric',
                    'agregado' => 'nullable|numeric',
                    'recinto' => 'nullable|numeric',
                    'ancho_caseta' => 'nullable|numeric',
                    'largo_caseta' => 'nullable|numeric',
                    'alto_caseta' => 'nullable|numeric',
                    'id_parcela_pp' => 'nullable',
                    'pais' => 'required|string',
                ]);
            }

            // Obtener todos los datos validados
            $data = $request->all();

            // Si el país es Portugal, eliminamos campos de España (y viceversa)
            if ($data['pais'] == 'portugal') {
                // Campos de España que deben ser NULL en DB
                $camposEspana = [
                    'municipio', 'provincia', 'numero_sigpac',
                    'poligono', 'parcela', 'zona', 'agregado', 'recinto'
                ];
                foreach ($camposEspana as $campo) {
                    $data[$campo] = null; // Forzamos NULL en lugar de unset
                }
            } else {
                // Campos de Portugal que deben ser NULL en DB
                $camposPortugal = ['concelho', 'distrito'];
                foreach ($camposPortugal as $campo) {
                    $data[$campo] = null;
                }
            }

            // Actualizamos la parcela con los datos filtrados
            $parcela->update($data);

            return response()->json(['message' => 'Parcela actualizada con éxito'], 200);
        } catch (ValidationException $e) {
        $errors = $e->validator->errors()->all();
        return response()->json(['status' => 422, 'errors' => $errors], 422);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }
    public function obtenerFiltros()
    {
        $filtros = Filtro::all();
        return response()->json($filtros, 200);
    }

    /**
     * Borrar una parcela. Pero lo hace con soft delte es decir que se asigna una fecha de baja y las relaciones quedan intactas
     */
    public function borrar($id)
    {
        $parcela = Parcela::find($id);
        $this->esAdminOPropietario($parcela);
        if (!$parcela) {
            return response()->json(['message' => 'Parcela no encontrada'], 404);
        }
        $parcela->fecha_baja = now();
        if ($parcela->save()) return response()->json(['message' => 'Parcela eliminada con éxito'], 200);
        else return  response()->json(['message' => 'No se pudo dar de baja'], 500);
        // $parcela->filtros()->detach();
        // $parcela->cultivos()->detach(); //esto de momento no podemos hacerlo por la cantidad de relaciones que tienen los cultivos por tanto cuando eliminamos parcela
        //Cuando eliminamos parcela lo unico que hacemos es ponerle fecha_bajao
        // $parcela->delete();


    }

    /**
     * Devuelve todos los municipios de una provincia.
     * Ayuda a generar el número SIGPAC.
     */
    public function obtenerMunicipios($idProvincia)
    {
        $municipios = Municipio::where('id_provincia', $idProvincia)->get(['id', 'nombre']);
        if ($municipios->isEmpty()) {
            return response()->json(['message' => 'No se encontraron municipios para la provincia seleccionada'], 404);
        }

        return response()->json($municipios, 200);
    }

    /**
     * Devuelve toda la información de las provincias.
     */
    public function obtenerProvincias()
    {
        $provincias = Provincia::all();
        return response()->json($provincias, 200);
    }

    public function obtenerTanques($idParcela)
    {
        try {
            $parcela = Parcela::with('tanques')->findOrFail($idParcela);

            // Validar permisos
            $this->esAdminOPropietario($parcela);
            if ($parcela->fecha_baja !== null) {
                return response()->json(['message' => 'Esta parcela está dada de baja'], 404);
            }

            $tanques = $parcela->tanques;

            return response()->json($tanques, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }

    public function crearTanque(Request $request)
    {
        try {
            $request->validate([
                'id_sensor' => 'nullable|numeric',
                'alto' => 'required|numeric',
                'ancho' => 'nullable|numeric',
                'largo' => 'nullable|numeric',
                'volumen' => 'nullable|numeric',
                'porcentaje_P' => 'required|numeric',
                'porcentaje_N' => 'required|numeric',
                'porcentaje_K' => 'required|numeric',
                'nombre_fert' => 'nullable|string|max:50',
                'id_parcela' => 'required|exists:parcelas,id',
            ]);
            $data = $request->all();
            // Verificar permisos
            $parcela = Parcela::findOrFail($data['id_parcela']);
            $this->esAdminOPropietario($parcela);

            // Si están presentes los tres valores, se calcula el volumen
            if (!empty($data['alto']) && !empty($data['ancho']) && !empty($data['largo'])) {
                $data['volumen'] = $data['alto'] * $data['ancho'] * $data['largo'];
            }
            $tanque = TanqueFertilizante::create($data);

            return response()->json($tanque, 201);
        } catch (ValidationException $e) {
            // Capturamos la excepción de validación
            $errors = $e->validator->errors()->all(); // Obtener solo los mensajes de error como un array

            // Retornamos la respuesta en el formato deseado
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }
    public function editarTanque(Request $request, $idTanque)
    {
        try {
            // Buscar el tanque o fallar si no existe
            $tanque = TanqueFertilizante::findOrFail($idTanque);

            // Validar datos, todos opcionales
            $request->validate([
                'id_sensor' => 'nullable|numeric',
                'alto' => 'nullable|numeric',
                'ancho' => 'nullable|numeric',
                'largo' => 'nullable|numeric',
                'volumen' => 'nullable|numeric',
                'porcentaje_P' => 'nullable|numeric',
                'porcentaje_N' => 'nullable|numeric',
                'porcentaje_K' => 'nullable|numeric',
                'nombre_fert' => 'nullable|string|max:50',
                'id_parcela' => 'nullable|exists:parcelas,id',
            ]);

            $data = $request->all();

            //$parcela = $tanque->parcela; // Asumiendo que tienes esta relación definida
            //$this->esAdminOPropietario($parcela); // Lanza 403 si no tiene permiso

            // Actualizar el tanque con los datos validados
            $tanque->update($data);

            return response()->json($tanque, 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()], 500);
        }
    }
    public function eliminarTanque($idTanque)
    {
        $tanque = TanqueFertilizante::find($idTanque);

        if (!$tanque) {
            return response()->json(['error' => 'Tanque no encontrado'], 404);
        }

        //$parcela = $tanque->parcela; // Asumiendo que tienes esta relación definida
        //$this->esAdminOPropietario($parcela); // Lanza 403 si no tiene permiso
        $tanque->delete();

        return response()->json(['mensaje' => 'Tanque eliminado correctamente']);
    }
}
