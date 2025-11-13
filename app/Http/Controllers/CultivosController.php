<?php

namespace App\Http\Controllers;

use App\Models\CultivoParcela;
use App\Models\Fertilizante;

use App\Models\AperoCultivo;
use App\Models\Riego;
use App\Models\Cultivo;
use App\Models\Fitosanitario;
use App\Models\GrupoBombeo;
use App\Models\Maquina;
use App\Models\Apero;
use App\Models\Espaldera;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Auth;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;


class CultivosController extends Controller //Controlador CultivoParcela
{
    // crear, editar, borrar, obtener (id_parcela, id_cultivo), dar de baja


    public function obtenerTiposCultivo()
    {
        $c = Cultivo::all();
        return response()->json($c, 200);
    }
    public function obtenerTiposRiego()
    {
        $f = Riego::all();
        return response()->json($f, 200);
    }
    public function obtenerGruposBombeo()
    {
        $f = GrupoBombeo::select(['id', 'potencia_bomba', 'cabeas'])->get();
        return response()->json($f, 200);
    }
    public function obtenerMaquinaria()
    {
        $f = Maquina::select(['id', 'nombre', 'tipo_cultivo', 'cv', 'kw'])->get();
        return response()->json($f, 200);
    }
    public function obtenerAperos()
    {
        $f = Apero::select(['id', 'nombre'])->get();
        return response()->json($f, 200);
    }
    public function obtenerEspalderas()
    {
        $f = Espaldera::all();
        return response()->json($f, 200);
    }

    public function obtenerTiposFitosanitarios()
    {
        $f = Fitosanitario::select(['id', 'nombre', 'materia_activa'])->get();
        return response()->json($f, 200);
    }
    public function obtenerSeleccionables()
    {
        try {
            $r = Riego::all();
            $g = GrupoBombeo::select(['id', 'potencia_bomba', 'cabeas'])->get();
            $m = Maquina::select(['id', 'nombre', 'tipo_cultivo', 'cv', 'kw'])->get();
            $a = Apero::select(['id', 'nombre'])->get();
            $e = Espaldera::all();
            $f = Fitosanitario::select(['id', 'nombre', 'materia_activa'])->get();
            $respuesta['riegos'] = $r;
            $respuesta['grupos'] = $g;
            $respuesta['maquinas'] = $m;
            $respuesta['aperos'] = $a;
            $respuesta['espalderas'] = $e;
            $respuesta['fitosanitarios'] = $f;
            return response()->json($respuesta);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }
    /**
     * Obtiene todas los cultivo_parcela.
     */
    public function obtenerTodas()
    {
        $parcelas = CultivoParcela::all();
        return response()->json($parcelas, 200);
    }
    public function obtenerResumenCultivosParcela($idParcela)
    {
        // Obtener cultivos para un usuario específico
        $cultivos = CultivoParcela::where('id_parcela', $idParcela)
            ->with([
                'cultivo' => function ($query) {
                    // Solo seleccionar el tipo de cultivo
                    $query->select('id', 'nombre');
                }
            ])->whereNull('fecha_baja')->select('id', 'id_cultivo', 'fecha_siembra', 'fecha_recoleccion')->get();

        return response()->json($cultivos, 200);
    }

    /**
     * Obtiene el cultivo de una parcela.
     */
    public function obtenerCultivoPorID($id)
    {
        // Obtener cultivo con todas sus relaciones, pero sin cargar el informe_impacto
        $cultivo = CultivoParcela::with([
            'cultivo:id,nombre',
            'parcela:id,id_agricultor,nombre',
            'espaldera:id,nombre',
            'sistemaRiego:id,nombre',
            'grupoBombeo:id',
            'fertilizantes',
            'fitosanitarios:id,nombre,materia_activa,fitosanitarios_cultivo.n_aplicaciones,fitosanitarios_cultivo.fecha',
            'pasesAperos:*', // Obtener apero asociado
            'pasesAperos.apero:id,nombre', // Obtener máquina asociada al apero
            // 'maquinas:id,nombre,maquinaria_cultivo.horas,maquinaria_cultivo.fecha',
        ])
            ->select(
                'id',
                'id_parcela',
                'id_cultivo',
                'fecha_siembra',
                'fecha_recoleccion',
                'espaldera',
                'n_pisos',
                'id_sector_pp',
                'sistema_riego',
                'n_goteros_arbol',
                'n_sectores',
                'caudal_gotero',
                'bomba',
                'combustible_bomba',
                'entre_arboles',
                'entre_calles',
                'superficie_cultivada',
                'dias_ciclo',
                'agua',
                'cosecha',
                'produccion_t_ha',
                'distancia_transporte_tr',
                'distancia_transporte_c',
                'distancia_fitosanitarios',
                'n_sectores',

            )->findOrFail($id);
        //Ahora la propiedad tiene_informe_impacto se añade automaticamente ya que está definida en el model como una propiedad $appends
        //con lo cual al devolver el objeto laravel lo añade.

        //Esta accion ya no hace falta porque hemos añadido un helper en el modelo del cultivo parcela que obtiene solo la confirmación de existencia
        //del informe impacto para evitar cargarlo cada vez que se optiene información del cultivo.
        // Añadir una propiedad temporal `tiene_informe_impacto`
        // $cultivo->tiene_informe_impacto = !is_null($cultivo->informe_impacto);

        // // Eliminar el campo `informe_impacto` del resultado
        // unset($cultivo->informe_impacto);

        // $cultivo-> fitosanitarios = $fitosanitariosPivot;
        //     $cultivo->aperos = $aperosPivot;
        //     $cultivo->maquinas = $maquinasPivot;
        $cultivo->aperosAgri = $cultivo->aperosDelAgricultor();
        $cultivo->maquinasAgri = $cultivo->maquinasDelAgricultor();

        return response()->json($cultivo, 200);
    }
    // public function obtenerPorId($idParcela)
    // {
    //     try {

    //         $cultivoParcela = CultivoParcela::with([
    //             'parcela',
    //             'cultivo',
    //             'espaldera',
    //             'sistemaRiego',
    //             'grupoBombeo',
    //             //'fertilizantes', //hay que arreglar esto
    //             //'fitosanitarios',
    //             'aperos',
    //             //'maquinas'
    //         ])->where('id_parcela', $idParcela)->where('fecha_baja', NULL)->get();

    //         return response()->json($cultivoParcela, 200);
    //     } catch (Exception $e) {
    //         return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
    //     }
    // }

    /**
     * Inserta un  cultivo_parcela en una parcela.
     */
    public function crearCultivoParcela(Request $request)
    {
        try {
            $request->validate([
                'id_parcela' => 'required|integer|exists:parcelas,id',
                'id_cultivo' => 'required|integer|exists:cultivos,id',
                'fecha_baja' => 'nullable|date',
                'espaldera' => 'nullable|numeric',
                'sistema_riego' => 'nullable|numeric',
                'bomba' => 'nullable|integer|exists:grupos_bombeo,id',
                'entre_arboles' => 'nullable|numeric|between:0,99999.99',
                'entre_calles' => 'nullable|numeric|between:0,99999.99',
                'goteros_arbol' => 'nullable|numeric|between:0,10',
                'superficie_cultivada' => 'nullable|numeric|between:0,99999.99',
                'produccion_t_ha' => 'nullable|numeric|between:0,99999.99',
            ]);

            // Comprobar si ya existe un registro con el mismo id_parcela y fecha_baja es NULL
            $existingCultivo = CultivoParcela::where(['id_parcela' => $request->id_parcela, 'id_cultivo' => $request->id_cultivo])
                ->whereNull('fecha_baja')
                ->first();

            if ($existingCultivo) {
                return response()->json(['status' => 422, 'message' => 'No se puede insertar: ya existe un cultivo igual en la misma parcela y no está dado de baja.'], 422);
            }
            // Aquí puedes proceder con la inserción
            $cultivoParcela = CultivoParcela::create($request->all());

            $tipoCultivo = Cultivo::find($request->id_cultivo);
            $cultivoParcela['cultivo'] = $tipoCultivo;
            return response()->json([
                'status' => true,
                'message' => 'Cultivo_parcela insertado con éxito',
                'datos' => $cultivoParcela
            ], 201);
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
     * Edita un  cultivo_parcela en una parcela.
     */
    public function editarCultivoParcela(Request $request)
    {
        try {
            // Buscar el cultivo de la parcela por ID y verificar que no tenga fecha de baja
            $cultivoParcela = CultivoParcela::where('id', $request['id'])
                ->whereNull('fecha_baja')
                ->first();

            if (!$cultivoParcela) {
                return response()->json(['status' => 404, 'message' => 'Cultivo de parcela no encontrado.'], 404);
            }

            // Validar los datos de entrada
            $request->validate([
                'id_parcela' => 'required|integer|exists:parcelas,id',
                'id_cultivo' => 'required|integer|exists:cultivos,id',
                'fecha_baja' => 'nullable|date',
                'espaldera' => 'nullable|integer',
                'id_filtro' => 'nullable|integer',
                'fecha_siembra' => 'nullable|date',
                'fecha_recoleccion' => 'nullable|date',
                'id_maquina' => 'nullable|integer',
                'horas_maquina' => 'nullable|integer',
                'sistema_riego' => 'nullable|integer',
                'n_goteros_arbol' => 'nullable|integer',
                'id_sector_pp' => 'nullable',
                'bomba' => 'nullable|integer|exists:grupos_bombeo,id',
                'entre_arboles' => 'nullable|numeric|between:0,99999.99',
                'entre_calles' => 'nullable|numeric|between:0,99999.99',
                'goteros_arbol' => 'nullable|numeric|between:0,10',
                'superficie_cultivada' => 'nullable|numeric|between:0,99999.99',
                'produccion_t_ha' => 'nullable|numeric|between:0,99999.99',
            ], [
                'id_parcela.required' => "Debe indicar una parcela",
                'id_parcela.integer' => "La parcela debe ser un número entero",
                'id_parcela.exists' => "La parcela seleccionada no existe",

                'id_cultivo.required' => "Debe indicar un cultivo",
                'id_cultivo.integer' => "El cultivo debe ser un número entero",
                'id_cultivo.exists' => "El cultivo seleccionado no existe",

                'fecha_baja.date' => "La fecha de baja no tiene un formato válido",

                'espaldera.integer' => "La espaldera debe ser un número entero",

                'id_filtro.integer' => "El filtro debe ser un número entero",

                'fecha_siembra.date' => "La fecha de siembra no tiene un formato válido",

                'fecha_recoleccion.date' => "La fecha de recolección no tiene un formato válido",

                'id_maquina.integer' => "La máquina debe ser un número entero",

                'horas_maquina.integer' => "Las horas de máquina deben ser un número entero",

                'sistema_riego.integer' => "El sistema de riego debe ser un número entero",

                'n_goteros_arbol.integer' => "El número de goteros por árbol debe ser un entero",

                'bomba.integer' => "La bomba debe ser un número entero",
                'bomba.exists' => "La bomba seleccionada no existe",

                'entre_arboles.numeric' => "La distancia entre árboles debe ser un número",
                'entre_arboles.between' => "La distancia entre árboles debe estar entre 0 y 99999.99",

                'entre_calles.numeric' => "La distancia entre calles debe ser un número",
                'entre_calles.between' => "La distancia entre calles debe estar entre 0 y 99999.99",

                'goteros_arbol.numeric' => "El número de goteros por árbol debe ser un número",
                'goteros_arbol.between' => "El número de goteros por árbol debe estar entre 0 y 10",

                'superficie_cultivada.numeric' => "La superficie cultivada debe ser un número",
                'superficie_cultivada.between' => "La superficie cultivada debe estar entre 0 y 99999.99",

                'produccion_t_ha.numeric' => "La producción en t/ha debe ser un número",
                'produccion_t_ha.between' => "La producción en t/ha debe estar entre 0 y 99999.99",
            ]);
            //dd($request);
            // Filtrar solo los datos correspondientes a CultivoParcela y actualizarlos
            $cultivoData = $request->only([
                //'id_parcela', //Estas dos propiedades no deben de poder cambiarse porque esto puede hacer que el cultivo haga referencia a otra persona.
                //'id_cultivo', //o se cambie el tipo de cultivo cuando no debería.
                'fecha_baja',
                'espaldera',
                'n_pisos',
                'id_filtro',
                'id_maquina',
                'id_sector_pp',
                'horas_maquina',
                'sistema_riego',
                'n_goteros_arbol',
                'n_sectores',
                'caudal_gotero',
                'bomba',
                'combustible_bomba',
                'agua',
                'dias_ciclo',
                'cosecha',
                'entre_arboles',
                'entre_calles',
                'superficie_cultivada',
                'produccion_t_ha',
                'fecha_siembra',
                'fecha_recoleccion',
                'distancia_transporte_tr',
                'distancia_transporte_c',
                'distancia_fitosanitarios',
                'n_sectores',
            ]);
            $cultivoParcela->update($cultivoData);

            // Llamar a las funciones que manejan las relaciones

            if ($request->has('fertilizantes')) {
                $this->insertarFertilizantes($cultivoParcela, $request->fertilizantes);
            }

            if ($request->has('fitosanitarios')) {
                $this->insertarFitosanitarios($cultivoParcela, $request->fitosanitarios);
            }

            if ($request->has('aperos')) {
                $this->insertarPasesAperos($cultivoParcela, $request->aperos); //En la request sen envían aperos como pases de aperos
            }

            if ($request->has('maquinas')) {
                $this->insertarHorasTransporteMaquinas($cultivoParcela, $request->maquinas);
            }

            return response()->json(['status' => true, 'message' => 'Cultivo de parcela actualizado con exito', 'datos' => $cultivoParcela], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        } catch (Exception $e) {

            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }

    public function eliminarCultivo($id)
    {
        try {
            $c = CultivoParcela::find($id);
            if ($c) {
                if ($c->fecha_baja == null) { //primero soft-delete
                    $c->fecha_baja = now();
                    $c->save();
                } else { //si ya se ha puesto fecha_baja previamente borramos definitivamente
                    // Desasociar todas las relaciones de muchos a muchos para evitar restricciones de clave foránea
                    $c->fertilizantes()->detach();
                    $c->fitosanitarios()->detach();
                    $c->pasesAperos()->detach();
                    $c->maquinas()->detach();

                    // Eliminación definitiva
                    $c->delete();
                }
                return response()->json(['status' => 200, 'mensaje' => 'Eliminado'], 200);
            } else {
                return response()->json(['status' => 402, 'mensaje' => 'No se encuentra el cultivo']);
            }
        } catch (Exception $e) {

            return response()->json(['status' => 500, 'mensaje' => $e->getMessage()]);
        }
    }

    protected function insertarFertilizantes($cultivoParcela, $fertilizantes)
    {
        Fertilizante::updateOrCreate(
            [
                'id_cultivo_parcela' => $cultivoParcela->id // Clave única para el fertilizante
            ],
            [
                'nombre_fertilizante' => $fertilizantes['nombre_fertilizante'] ?? null,
                'uds_N' => $fertilizantes['uds_N'] ?? null,
                'uds_P' => $fertilizantes['uds_P'] ?? null,
                'uds_K' => $fertilizantes['uds_K'] ?? null,
                'porcentaje_N' => $fertilizantes['porcentaje_N'] ?? null,
                'porcentaje_P' => $fertilizantes['porcentaje_P'] ?? null,
                'porcentaje_K' => $fertilizantes['porcentaje_K'] ?? null,
                'kg_ha' => $fertilizantes['kg_ha'] ?? 0,
                'km' => $fertilizantes['km'] ?? 0,
                'fecha' => $fertilizantes['fecha'] ?? now()
            ]
        );
        //Vamos a usar la tabla de aplicaciones.

    }
    protected function insertarFitosanitarios($cultivoParcela, $fitosanitarios)
    {
        // $conta = 0;
        // $syncDat = [];
        // foreach ($fitosanitarios as $fitosanitario) {
        //     $syncDat[$conta] = ([
        //         'id_fitosanitario' => $fitosanitario['id'],
        //         'n_aplicaciones' => $fitosanitario['n_aplicaciones'],
        //         'fecha' => isset($fitosanitario['fecha']) ? substr($fitosanitario['fecha'], 0, 10) : now()
        //     ]
        //     );
        //     $conta++;
        // }

        // $cultivoParcela->fitosanitarios()->sync($syncDat);
        $syncDat = [];
        foreach ($fitosanitarios as $fitosanitario) {
            $syncDat[$fitosanitario['id']] = [
                'n_aplicaciones' => $fitosanitario['n_aplicaciones'],
                'fecha' => isset($fitosanitario['fecha']) ? substr($fitosanitario['fecha'], 0, 10) : now(),
            ];
        }

        $cultivoParcela->fitosanitarios()->sync($syncDat);


    }

    public function agregarInforme(Request $request, $id)
    {
        try {

            $arvicho = $request->file('archivo'); ///EL ARVHICHO HOEPUTA sin el validator no funciona no me preguntes porque...
            if (!$arvicho) {
                $arvicho = $request->input('archivo'); //esto debería petar porque debe ser explicitamente un file para poder obtener el content
            }
            $content = file_get_contents($arvicho->getRealPath()); //necesito usar esta función para almacenar el contenido del archivo en una entrada de la tabla
            $c_p = CultivoParcela::find($id);

            $c_p->informe_impacto = $content;
            if ($c_p->save()) {
                $respuesta = array('estado' => true, 'status' => 201, 'mensaje' => 'Informe añadido');
            } else {
                $respuesta = array('estado' => false, 'stauts' => 300, 'mensaje' => 'No se ha podido añadir el archivo');
            }


            return response()->json($respuesta);
            //return response($content)->header ('Content-Type', 'application/pdf');
        } catch (Exception $e) {

            LogController::errores('[AÑADIR ARCHIVO A FACTURA EXTERNA] ' . $e->getMessage());
            return response()->json(['estado' => false, 'mensaje' => $e->getMessage(), 'e' => $e->getTraceAsString()], 200);
        }
    }
    public function obtenerArchivo($id)
    {
        try {
            // Obtener solo el campo `informe_impacto` del cultivo por su ID
            $cultivo = CultivoParcela::select('informe_impacto')->findOrFail($id);

            // Asegurarse de que el archivo exista
            if (is_null($cultivo->informe_impacto)) {
                return response()->json(['estado' => false, 'mensaje' => 'El archivo no existe.'], 404);
            }

            // Devolver el archivo con el tipo de contenido adecuado
            return response($cultivo->informe_impacto)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="informe_impacto_' . $id . '.pdf"');
        } catch (Exception $e) {
            // Log de errores y respuesta en caso de excepción
            LogController::errores('[OBTENER ARCHIVO] ' . $e->getMessage());
            return response()->json(['estado' => false, 'mensaje' => $e->getMessage(), 'e' => $e->getTraceAsString()], 500);
        }
    }

    public function borrarArchivo($id)
    {
        try {
            // Obtener solo el campo `informe_impacto` del cultivo por su ID
            $archivoCultivo = CultivoParcela::where('id', $id)->update('informe_impacto', null);

            //$content = file_get_contents($arvicho->archivo);
            if ($archivoCultivo > 0) {
                $respuesta = array('estado' => true, 'status' => 200, 'mensaje' => 'Se ha eliminado correctamente el archivo');
            } else {
                $respuesta = array('estado' => true, 'status' => 204, 'mensaje' => 'No se ha eliminado nigun archivo');
            }
            return response()->json($respuesta);
            //return response($content)->header ('Content-Type', 'application/pdf');
        } catch (Exception $e) {
            //dd(e);
            LogController::errores('[ELIMINANDO ARCHIVO] ' . $e->getMessage());
            return response()->json(['estado' => false, 'mensaje' => $e->getMessage(), 'e' => $e->getTraceAsString()], 200);
        }
    }

    protected function insertarPasesAperos($cultivoParcela, $aperos)
    {


        if (!is_array($aperos)) {
            throw new \Exception("El campo aperos debe ser un array.");
        }

        foreach ($aperos as $index => $apero) {

            if (!is_array($apero)) {
                throw new \Exception("El valor en aperos[$index] no es válido.");
            }
            if (!isset($apero['id_apero'])) {
                throw new \Exception("El apero en la posición $index no tiene 'id'.");
            }

            AperoCultivo::updateOrCreate(
                [
                    'id_cultivo_parcela' => $cultivoParcela->id,
                    'id_apero' => $apero['id_apero']
                ],
                [
                    'id_maquina' => $apero['id_maquina'] ?? null,
                    'pases' => $apero['pases'] ?? 1,
                    'fecha' => isset($apero['fecha']) ? date('Y-m-d H:i:s', strtotime($apero['fecha'])) : now()->toDateTimeString()
                ]
            );
        }
    }


    protected function insertarHorasTransporteMaquinas($cultivoParcela, $maquinas)
    {
        $conta = 0;
        foreach ($maquinas as $maquina) {
            $syncData[$conta] = ([
                'id_maquina' => $maquina['id'],
                'horas' => $maquina['horas'],
                'fecha' => isset($maquina['fecha']) ? substr($maquina['fecha'], 0, 10) : now(),
            ]
            );
            $conta++;
        }
        $cultivoParcela->maquinas()->sync($syncData);
    }
    protected function generarInformeRiegos($cultivo)
    {
        $informe['tipo'] = $cultivo->sistemaRiego->nombre;
        $informe['entre_arboles'] = $cultivo->entre_arboles;
        $informe['entre_calles'] = $cultivo->entre_calles;
        $informe['n_goteros_arbol'] = $cultivo->n_goteros_arbol;
        $divisor = ($cultivo->entre_arboles * $cultivo->entre_calles);
        if ($cultivo->entre_arboles && $cultivo->entre_calles) {
            $informe['n_arboles'] = (10000 / $divisor);

            $informe['n_calles'] = 100 / ($cultivo->entre_arboles);
        } else {
            $informe['n_arboles'] = "No se pueede calcular si distancia entre arboles";
            $informe['n_calles'] = "No se puede calcular la dista entre calles";
        }
        $informe['m_portagotero'] = 100 * $informe['n_calles'];
        $informe['peso_portagoteros_16mm'] = $informe['m_portagotero'] * 0.0563;
        $informe['n_enganches'] = $informe['n_calles']; //un enganche por calle
        $informe['peso_enganches'] = round(($informe['n_enganches'] * 0.152), 2); //peso de las piezas de reducción 32 a 16
        $informe['metros_principal'] = 100;

        $informe['peso_principal_32mm'] = 100 * 0.2107; //Peso de tubería de 32 mm rígida (1m) (peso en laboratorio)
        $informe['peso_llaves'] = 1 * 0.0208; //peso de las llaves y s siempre hay una

        $informe['kg_PP'] = $informe['peso_enganches'] + $informe['peso_llaves'];
        if ($cultivo->sistemaRiego->id == 3) {
            $informe['peso_tira_pollo'] = 3 * 26;
            $informe['peso_enganches'] = round(($informe['n_enganches'] * 0.01165), 2); //peso de la unión sin llaves
            $informe['peso_principal_17mm'] = 100 * 1.105; //metros de principal * peso de tubería plana azul 127mm
            $informe['deposito_abono'] = 55 / 10; //peso de un depostito de abono de 1000l (55kg) en una finca estandard de 10ha


            $informe['kg_PP_ha_anio'] = $informe['kg_PP'] / 8;

            $informe['kg_PE_1_ha_anio'] = $informe['deposito_abono'] / 5; //5 años de vida util del deposito de abono
            $informe['kg_PE_2_ha_anio'] = $informe['peso_tira_pollo'] / 1; //1 año de vida util de la tira de pollo
            $informe['kg_PE_deposito_ha_anio'] = 6.5 / 15; //15 años de vida util de un deposito de 65kg de 1000l (será de agua... para una parcela de 10ha)
            $informe['kg_PE_ha_anio'] = $informe['kg_PE_1_ha_anio'] + $informe['kg_PE_2_ha_anio'] + $informe['kg_PE_deposito_ha_anio'];
            $informe['kg_PVC_ha_anio'] = $informe['peso_principal_17mm'] / 8; //8 años de vida util de la tuberia principal plana
            if ($cultivo->produccion_t_ha) {
                $informe['kg_PVC_produccion'] = $informe['kg_PVC_ha_anio'] / $cultivo->produccion_t_ha;
            } else {
                $informe['kg_PVC_produccion'] = "falta la producción en toneladas por hectarea";
            }
        } else {
            $informe['kg_PE'] = $informe['peso_portagoteros_16mm'] + $informe['peso_principal_32mm'];
            if ($cultivo->sistemaRiego->id == 1) {
                $informe['numero_goteros'] = $informe['n_goteros_arbol'] * $informe['n_arboles'];
                //0.00734*$informe ['n_goteros_arbol'] peso de los goteros en caso de ser goteros pinchados y contabilizarlos dentro del PP
                $informe['kg_PP'] = $informe['kg_PP'] + 0.00734 * $informe['numero_goteros'];
            }

            if ($cultivo->sistemaRiego->id == 1) {
                $informe['kg_PP_ha_anio'] = $informe['kg_PP'] / 30; //30 años de vida util en goteros autocompensados
                $informe['kg_PE_ha_anio'] = $informe['kg_PE'] / 30;
            } else if ($cultivo->sistemaRiego->id == 2) {
                $informe['kg_PP_ha_anio'] = $informe['kg_PP'] / 20; //30 años de vida util en goteros integrados
                $informe['kg_PE_ha_anio'] = $informe['kg_PE'] / 20;
            }
        }
        if ($cultivo->produccion_t_ha) {
            $informe['kg_PP_produccion'] = $informe['kg_PP_ha_anio'] / $cultivo->produccion_t_ha;
            $informe['kg_PE_produccion'] = $informe['kg_PE_ha_anio'] / $cultivo->produccion_t_ha;
        } else {
            $informe['kg_PE_produccion'] = "falta la producción en toneladas por hectarea";
        }

        return $informe;
    }
    protected function generarInformeEspalderas($cultivo)
    {
        $informe['tipo'] = $cultivo->espaldera;
        $informe['entre_arboles'] = $cultivo->entre_arboles;
        $informe['entre_calles'] = $cultivo->entre_calles;
        $informe['ditancia_postes'] = 10;
        $informe['postes_linea'] = 100 / ($informe['ditancia_postes'] * $informe['entre_arboles']);
        $informe['peso_poste'] = 1.4; //peso del poste aportado por juanma
        $informe['lineas_ha'] = 100 / $informe['entre_calles'];

        if ($cultivo->espaldera == 1) {
            $informe['postes_ha'] = round(($informe['lineas_ha'] * $informe['postes_linea']), 2);
            $informe['gripple'] = 2 * $informe['lineas_ha'];
            $informe['peso_gripple'] = $informe['gripple'] * 0.05; //peso del gripple m = 0.05
            $informe['peso_alambre_ha'] = $informe['lineas_ha'] * 100 * 0.0444; //peso de 1m de alambre = 0.0444
            $informe['peso_postes_ha'] = $informe['postes_ha'] * $informe['peso_poste'];
            $informe['kg_acero_ha'] = round(($informe['peso_alambre_ha'] + $informe['peso_gripple'] + $informe['peso_postes_ha']), 2);
            $informe['kg_acero_ha_anio'] = $informe['kg_acero_ha'] / 20;
        } else {
            $informe['postes_ha'] = (100 / 6) * $informe['lineas_ha']; //entendemos que hay postes cada 6 metros
            // $informe['peso_postes_ha'] = (100/6) * $informe['peso_poste'] * $informe['lineas_ha'];
            $informe['m_alambre_formacion_ha'] = 100 * $informe['lineas_ha'];
            $informe['peso_alambre_formacion_ha'] = $informe['m_alambre_formacion_ha'] * 0.0444; //0.0444 = peso de alambre de formación de 2.4mm
            $informe['gripple_formacion_ha'] = 2 * $informe['lineas_ha'];
            $informe['peso_gripple_formacion_ha'] = $informe['gripple_formacion_ha'] * 0.05; // 0.5 peso de griple m
            $informe['gripple_vegetacion_ha'] = 2 * $informe['lineas_ha'] * $cultivo->n_pisos;
            $informe['peso_gripple_vegetacion_ha'] = $informe['gripple_vegetacion_ha'] * 0.03; //0.03 peso griple s
            $informe['peso_gripple_total_ha'] = $informe['peso_gripple_formacion_ha'] + $informe['peso_gripple_vegetacion_ha'];

            $informe['m_alambre_vegetacion_ha'] = 100 * $informe['lineas_ha'] * $cultivo->n_pisos;
            $informe['peso_alambre_vegetacion_ha'] = $informe['m_alambre_vegetacion_ha'] * 0.0198; //0.0198 = peso de alambre de vegetacion de 1.8mm

            $informe['peso_postes_ha'] = $informe['postes_ha'] * $informe['peso_poste'];

            $informe['kg_acero_ha'] = $informe['peso_alambre_vegetacion_ha'] + $informe['peso_alambre_formacion_ha'] + $informe['peso_gripple_total_ha'] + $informe['peso_postes_ha'];


            $informe['kg_acero_ha_anio'] = $informe['kg_acero_ha'] / 35;

            // $informe['m_alambre_vegetacion_ha'] = $informe['lineas_ha'] * 100 *
        }

        $informe['kg_acero_ha_anio_produccion'] = $informe['kg_acero_ha_anio'] / $cultivo->produccion_t_ha;

        return $informe;
    }
    protected function generarInformeFitosanitarios($cultivo)
    {
        //iterar todos los fitosanitarios y agrupar la cantidad empleada de cada fitosanitario en una cantidad total segun la clasificacion simapro
        //si hay varios fitosanitario de la misma clasificación simapro sumamos las cantidades empleadas y mostramos una sola cantidad para esa clasificacion.
        $informe = [];
        //dd($cultivo->fitosanitarios);
        foreach ($cultivo->fitosanitarios as $f) {
            //echo($f);
            if (isset($informe[$f->clasificacion_simapro])) {
                $aux = $f->n_aplicaciones * $f->densidad * ($f->porcentaje_ma / 100);
                switch ($cultivo->id_cultivo) {
                    case 1:
                        $aux = $aux * $f->max_olivo_L_ha;
                        break;
                    case 2:
                        $aux = $aux * $f->max_vid_L_ha;
                        break;
                    case 3:
                        $aux = $aux * $f->max_arroz_L_ha;
                        break;
                    case 4:
                        $aux = $aux * $f->max_tomate_L_ha;
                        break;
                        break;
                    default:
                        $aux = $aux * $f->max_frutal_L_ha;
                        break;
                }
                $informe[$f->clasificacion_simapro] += $aux / $cultivo->produccion_t_ha; //acumulamos con esa materia activa que ya ha aparecido en otro fitosanitario
                // $informe[$f->clasificacion_simapro]
            } else {
                $aux = $f->n_aplicaciones * $f->densidad * ($f->porcentaje_ma / 100);
                switch ($cultivo->id_cultivo) {
                    case 1:
                        $informe[$f->clasificacion_simapro] = $aux * $f->max_olivo_L_ha;
                        break;
                    case 2:
                        $informe[$f->clasificacion_simapro] = $aux * $f->max_vid_L_ha;
                        break;
                    case 3:
                        $informe[$f->clasificacion_simapro] = $aux * $f->max_arroz_L_ha;
                        break;
                    case 4:
                        $informe[$f->clasificacion_simapro] = $aux * $f->max_tomate_L_ha;
                        break;
                        break;
                    default:
                        $informe[$f->clasificacion_simapro] = $aux * $f->max_frutal_L_ha;
                        break;
                }
                $informe[$f->clasificacion_simapro] = $informe[$f->clasificacion_simapro] / $cultivo->produccion_t_ha;
            }
        }
        return $informe;
    }
    protected function generarInformeFertilizantes($cultivo)
    {
        $informe = [];
        //  dd($cultivo->fertilizantes);
        if ($cultivo->fertilizantes->uds_N != null) {
            $informe['kg_N'] = $cultivo->fertilizantes->uds_N / $cultivo->produccion_t_ha;
        } else {
            $informe['kg_N'] = (($cultivo->fertilizantes->porcentaje_N / 100) * $cultivo->fertilizantes->kg_ha) / $cultivo->produccion_t_ha;
        }

        if ($cultivo->fertilizantes->uds_K != null) {
            $informe['kg_K2O'] = $cultivo->fertilizantes->uds_K * 1.2 / $cultivo->produccion_t_ha;
        } else {
            $informe['kg_K2O'] = (($cultivo->fertilizantes->porcentaje_K / 100) * $cultivo->fertilizantes->kg_ha) / $cultivo->produccion_t_ha;
        }
        if ($cultivo->fertilizantes->uds_P != null) {
            $informe['kg_P2O5'] = $cultivo->fertilizantes->uds_P * 2.29 / $cultivo->produccion_t_ha;
        } else {

            $informe['kg_P2O5'] = (($cultivo->fertilizantes->porcentaje_P / 100) * $cultivo->fertilizantes->kg_ha) / $cultivo->produccion_t_ha;
        }

        $informe['kg_NH3'] = $informe['kg_N'] * 0.0164;
        $informe['kg_N2O'] = $informe['kg_N'] * 0.005;
        $informe['kg_NOX'] = $informe['kg_N'] * 0.04;
        $informe['kg_NO3'] = $informe['kg_N'] * 0.3;

        $informe['transporte_fert_UF_1'] = $cultivo->fertilizantes->km / $cultivo->produccion_t_ha;

        return $informe;
    }
    protected function generarInformeAperos($cultivo)
    {

        // foreach ($cultivo->aperos as $a) {
        foreach ($cultivo->pasesAperos as $a) {

            //rendimiento h/ha (E3) = tiempo_labor_h_ha(C) * n_pases (D)
            $informe[$a->apero->labores_simapro]['rendimeinto_h_ha'] = $a->apero->tiempo_m_labor * $a->pases;
            //UF_1_ha(G) =rendimiento h/ha (E) / rendimiento_simapro(h/ha) (F)
            $informe[$a->apero->labores_simapro]['UF_1_ha'] = $informe[$a->apero->labores_simapro]['rendimeinto_h_ha'] * $a->apero->rendimiento_simapro;
            //UF_1_ha_produccion (I) = UF_1_ha (G) /cultivo->produccion_t_ha;
            $informe[$a->apero->labores_simapro]['UF_1_ha_produccion'] = $informe[$a->apero->labores_simapro]['UF_1_ha'] / $cultivo->produccion_t_ha;
            //fabricacion (L) = rendimiento h/ha(E) * peso;
            $informe[$a->apero->labores_simapro]['fabricacion'] = $informe[$a->apero->labores_simapro]['rendimeinto_h_ha'] * $a->apero->peso / $a->apero->vida;
            //reparacion (M) = fabricacion * 0.45;
            $informe[$a->apero->labores_simapro]['reparacion'] = $informe[$a->apero->labores_simapro]['fabricacion'] * 0.45;
            $informe[$a->apero->labores_simapro]['UF_1_kg'] = $informe[$a->apero->labores_simapro]['fabricacion'] + $informe[$a->apero->labores_simapro]['reparacion'];
            $informe[$a->apero->labores_simapro]['UF_1_kg_produccion'] = $informe[$a->apero->labores_simapro]['UF_1_kg'] / $cultivo->produccion_t_ha;
            // UF_1_kg = L + M;
            // UF_1_kg_produccion = UF_1_kg / cultivo->produccion_t_ha;
        }
        $informe['ocupacion_suelo'] = 1 / ($cultivo->produccion_t_ha * (365 / $cultivo->dias_ciclo));
        $informe['uso_de_agua'] = $cultivo->agua / $cultivo->produccion_t_ha;

        return $informe;
    }
    protected function generarInformeMaquinaria($cultivo)
    {
        //debería obtener de la tabla de maquinarias para hacer referencia al los datos de la misma?
        $informe = [];

        if (!$cultivo->cochecha) {  //cosecha es una variable booleana que si está a true (1) indica que se ha realizado la cosecha mediante maquinaria
            if ($cultivo->id_cultivo == 3) { //arroz

                $informe['cosechadora_cereales']['UF_kg'] = 12.7296;
                $informe['cosechadora_cereales']['UF_kg_produccion'] = 12.7296 / $cultivo->produccion_t_ha;
                $informe['cosechadora_cereales']['UF_kg_fabricacion'] = 0.7425;
                $informe['cosechadora_cereales']['UF_kg_fabricacion_produccion'] = 0.7425 / $cultivo->produccion_t_ha;
            } else if ($cultivo->id_cultivo == 1 || $cultivo->id_cultivo == 2) { //olivo o vid mismo tipo de cosechadora
                $str = 'vid';
                if ($cultivo->id_cultivo == 1)
                    $str = 'olivo';

                $informe['cosechadora_' . $str]['UF_kg'] = 31.2;
                $informe['cosechadora_' . $str]['UF_kg_produccion'] = 31.2 / $cultivo->produccion_t_ha;
                $informe['cosechadora_' . $str]['UF_kg_fabricacion'] = 2.475;
                $informe['cosechadora_' . $str]['UF_kg_fabricacion_produccion'] = 2.475 / $cultivo->produccion_t_ha;
            } else if ($cultivo->id_cultivo == 4) { //tomate
                $informe['cosechadora']['UF_kg'] = 33.696;
                $informe['cosechadora']['UF_kg_produccion'] = 33.696 / $cultivo->produccion_t_ha;
                $informe['cosechadora']['UF_kg_fabricacion'] = 2.828571429;
                $informe['cosechadora']['UF_kg_fabricacion_produccion'] = 2.475 / $cultivo->produccion_t_ha;
            }
        }

        // No deberían hacer falta estos calculos ya que van incluidas en aperos. Si acaso habría que sumar
        //las horas de aperos para calcular el reciclaje de las maquinaria en función de las horas/ha que se haya usado
        //la maquina en total con los apero
        $rendimientoMaquina = [];
        $maquinas = [];
        foreach ($cultivo->pasesAperos as $a) {
            if ($a->id_maquina != null && !isset($rendimientoMaquina[$a->id_maquina])) {
                $rendimientoMaquina[$a->id_maquina] = $a->pases * $a->apero->tiempo_m_labor;
                if (!isset($maquinas[$a->id_maquina])) {
                    $maquinas[$a->id_maquina] = $a->maquina; //esto para saber que maquinas se usan en un cultivo me las guardo en el array
                }
            } else if (isset($rendimientoMaquina[$a->id_maquina]) && $rendimientoMaquina[$a->id_maquina] != 0) {
                $rendimientoMaquina[$a->id_maquina] += $a->pases * $a->apero->tiempo_m_labor;
            }
        }

        foreach ($maquinas as $m) {

            $informe[$m->nombre]['fabricacion_kg_ha'] = $m->peso * $rendimientoMaquina[$m->id] / $m->vida_h;
            $informe[$m->nombre]['reparacion_kg_ha'] = $informe[$m->nombre]['fabricacion_kg_ha'] * 0.2;
            $informe[$m->nombre]['UF_kg_reciclaje'] = $informe[$m->nombre]['fabricacion_kg_ha'] + $informe[$m->nombre]['reparacion_kg_ha'];
            $informe[$m->nombre]['UF_kg_reciclaje_produccion'] = $informe[$m->nombre]['UF_kg_reciclaje'] / $cultivo->produccion_t_ha;
        }

        //$informe ['transporte_camion'][]
        return $informe;


        //Quinta rueda se contabiliza como una labor que implica mover el tractor con un remolque lleno con la producción
        //con lo cual cuando en maquinaria se selccione quinta rueda daremos los datos de consumo preestipulados para esto.

    }
    /**
     * Recibe la información de la parcela en la variable $pa que dentro contiene el alto largo y ancho de la caseta
     */
    protected function generarInformeCaseta($pa, $produccion)
    {
        $informe['alto'] = $pa->alto_caseta;
        $informe['largo'] = $pa->largo_caseta;
        $informe['ancho'] = $pa->ancho_caseta;
        $informe['largo_tejado'] = $pa->largo_caseta;
        $informe['ancho_tejado'] = $pa->ancho_caseta;
        $informe['superficie_tejado'] = $informe['largo_tejado'] * $informe['ancho_tejado']; //a tener en cuenta que la superficie del tejado es igual que la superficie de la caseta.
        $informe['kg_hormigon_m2'] = 5857 / 10;
        $informe['kg_hormigon'] = $informe['kg_hormigon_m2'] * $informe['superficie_tejado'];
        $informe['peso_m2_chapas_acero'] = 11.4 - 1.75; //peso de 1m2 de chapa sandwich - peso de 1m2 de poliuretano
        $informe['kg_poliuretano'] = 1.75 * $informe['superficie_tejado']; //peso de 1m2 de poliuretano por la superfice del tejado que es donde habrá poliuretano por la chapa sandwich
        $informe['kg_acero'] = $informe['peso_m2_chapas_acero'] * $informe['superficie_tejado'];

        $informe['kg_hor'] = $informe['kg_hormigon'] / 50 / $produccion;
        $informe['kg_PU'] = $informe['kg_poliuretano'] / 50 / $produccion;
        $informe['kg_acero_ha_anio'] = $informe['kg_acero'] / 50 / $produccion;
        return $informe;
    }
    protected function generarInformeGrupoBombeo($cultivo)
    {
        $informe['cabeas'] = $cultivo->grupoBombeo->cabeas;
        $informe['potencia'] = $cultivo->grupoBombeo->potencia_bomba;
        $informe['consumo_l_h'] = $cultivo->grupoBombeo->diesel_l_h;
        $informe['kg_acero_ha_produccion'] = $cultivo->grupoBombeo->kg_acero / $cultivo->superficie_cultivada / $cultivo->produccion_t_ha;
        return $informe;
        //luego lo completo
    }
    public function generarInformeCultivo($id)
    {
        ini_set('precision', 2);
        $cultivo = CultivoParcela::with([
            'cultivo:id,nombre',
            'parcela:id,id_agricultor,nombre,alto_caseta,largo_caseta,ancho_caseta',
            'espaldera:id,nombre',
            'sistemaRiego:id,nombre',
            'grupoBombeo:id,cabeas,potencia_bomba,diesel_l_h,kg_acero',
            'fertilizantes',
            'fitosanitarios:*,fitosanitarios_cultivo.n_aplicaciones', //necesito obtener a parte del numero de aplicaicones del fito (tabla pivot), también toda la información de su tabla
            'pasesAperos.apero', // Obtener apero asociado
            'pasesAperos.maquina:id,nombre,peso,vida_h', // Obtener máquina asociada al apero
            //'maquinas:id,nombre,peso,vida_h,maquinaria_cultivo.horas,maquinaria_cultivo.fecha',
        ])
            ->select(
                'id',
                'id_parcela',
                'id_cultivo',
                'fecha_siembra',
                'fecha_recoleccion',
                'espaldera',
                'n_pisos',
                'sistema_riego',
                'n_goteros_arbol',
                'bomba',
                'combustible_bomba',
                'entre_arboles',
                'entre_calles',
                'superficie_cultivada',
                'dias_ciclo',
                'agua',
                'cosecha',
                'produccion_t_ha',
                'distancia_transporte_tr',
                'distancia_transporte_c',
                'distancia_fitosanitarios',
                'n_sectores'
            )
            ->findOrFail($id);
        //ALTER TABLE `users` ADD `cat_calorimetro` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'categoria visual entidades calorimetro' AFTER `cat_analizador_red`;
        $informe = [];

        $informe['riegos'] = $this->generarInformeRiegos($cultivo);
        if (!$cultivo->produccion_t_ha) {
            return response()->json(['estado' => false, 'mensaje' => 'faltan datos para completar los calculos'], 500);
        }
        if ($cultivo->espaldera) {

            $informe['espalderas'] = $this->generarInformeEspalderas($cultivo);
        }
        if ($cultivo->parcela->alto_caseta) {
            $informe['caseta'] = $this->generarInformeCaseta($cultivo->parcela, $cultivo->produccion_t_ha);
        }

        if ($cultivo->grupoBombeo)
            $informe['bombeo'] = $this->generarInformeGrupoBombeo($cultivo);

        $informe['fitosanitarios'] = $this->generarInformeFitosanitarios($cultivo);

        $informe['fertilizantes'] = $this->generarInformeFertilizantes($cultivo);

        $informe['manejo_cultivo'] = $this->generarInformeAperos($cultivo);
        $informe['maquinaria'] = $this->generarInformeMaquinaria($cultivo);

        return response()->json($informe);

        // $informe['fitosanitarios'] = $this->generarInformeFitosanitarios();
        // $informe['fertilizantes'] = $this->generarInformeFertilizantes();
        // $informe['aperos'] = $this->generarInformeAperos();
        // $informe['maquinaria'] = $this->generarInformeMaquinaria();
        // $informe['filtros'] = $this->generarInformeFiltros();
        // $informe['caseta_riego'] = $this->generarInformeCasetaRiego(); //Estos que pertenecerían al controlador de parcela al final los voy a meter dentro de este controlador para centralizar

    }

    public function enviarEmail($correo, $cultivoId)
    {
        // Buscar el cultivo en la BD
        $cultivo = CultivoParcela::with(['parcela.agricultor', 'cultivo'])->findOrFail($cultivoId);

        $name = $cultivo->parcela->agricultor->nombre;
        $parcela = $cultivo->parcela->nombre;
        $cultivo = $cultivo->cultivo->nombre;
        try {

            Mail::to($correo)->send(new Notification(
                $name,
                $parcela,
                $cultivo
            ));
            return response()->json(['estado' => true, 'mensaje' => 'Correo enviado correctamente.']);
        } catch (Exception $e) {
            LogController::errores('[ENVIANDO EMAIL] ' . $e->getMessage());
            return response()->json(['estado' => false, 'mensaje' => 'Error al enviar el correo: ' . $e->getMessage()], 500);
        }
    }
}
