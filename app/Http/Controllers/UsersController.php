<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use App\Models\Consents;
use App\Models\PolicyVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Exception;
use Namshi\JOSE\JWS;

class UsersController extends Controller
{
    // Registro de un nuevo usuario
    public function registroUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'accepted_terms' => 'required|accepted',
        ]);

        $user = new User([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'dni' => $request->dni ?? null,
            'telefono' => $request->telefono ?? null,
            'user_depend' => $request->user_depend ?? null,
            'rol' => 3 // Por defecto, un nuevo usuario tendrá el rol 'usuario'
        ]);

        if ($request['accept_terms'] == true) {
            // 2) Obtener la versión y hash actual de la política
            $policy = PolicyVersion::latest('id')->firstOrFail();

            // 3) Registrar el consentimiento, pasándole la IP
            Consents::recordAcceptance(
                $user->id,
                $policy->policy_version,
                $policy->policy_hash,
                $request->ip()
            );
            $user->save();

            return response()->json(['message' => 'Usuario registrado con exito'], 201);
        } else {
            return response()->json(['message' => 'Lo sentimos para utilizar esta aplicación es necesario que acepte los terminos'], 403);
        }

    }

    // Iniciar sesión (Login)
    public function login(Request $request)
    {
        try {

            $credentials = $request->only('email', 'password');
            try {
                // dd(JWTAuth::attempt($credentials));

                if (!$token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'Credenciales inválidas'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'No se pudo crear el token'], 500);
            }
            // $user = Auth::user();
            // $user['token'] = $token;
            // $user ['agricultor'] = Agricultor::where('id_usuario', $user->id)->get()->first();
            return response()->json(compact('token'), 200);
        } catch (Exception $e) {
            return response()->json(['status' => 403, 'mensaje' => $e->getMessage()]);
        }
    }

    // Crear un nuevo usuario (solo por administradores)
    public function crearUsuario(Request $request)
    {
        try {

            $request->validate([
                'nombre' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'rol' => 'numeric|in:1,2,3', //1 super admin , 2 admin, 3 usuario agricultor (solo el superadmin puede crear otros super y admins) y el admin puede crear usuarios
            ]);
            //si el rol del usuario a editar comprobamos que la edición la está llevando a cabo un superAdministrador
            if ($request['rol'] == 1 || $request['rol'] == 2) {
                $this->authorizeSuperAdmin();

            }

            $user = new User([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'dni' => $request->dni ?? null,
                'telefono' => $request->telefono ?? null,
                'rol' => $request->rol ?? 3,
                'user_depend' => $request->user_depend ?? null,
                'created_at' => now(),
            ]);
            $user->save();
            return response()->json(['message' => 'Usuario creado con exito', 'data' => $user], 201);
        } catch (ValidationException $e) {
            // Manejo específico de la excepción de validación
            $errors = $e->validator->errors();
            return response()->json(['errores' => $errors], 422);

        } catch (Exception $e) {
            // Manejo de cualquier otra excepción
            return response()->json([
                'message' => 'Ocurrió un error inesperado.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Registra un nuevo usuario agricultor.
     *
     * Este método valida los datos del formulario de registro, crea un nuevo usuario en la base de datos
     * con el rol de agricultor, y registra su aceptación de la política de privacidad si los términos son aceptados.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registrarUsuarioAgricultor(Request $request)
    {
        try {
            //$this->authorizeAdmin();

            $request->validate([
                'nombre' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|confirmed',
                'rol' => 'numeric|in:1,2,3',
            ], [
                'email.unique' => "El email ya existe",
            ]);

            $user = User::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'dni' => $request->dni ?? null,
                'telefono' => $request->telefono ?? null,
                'n_referencia' => null,
                'user_depend' => $request->user_depend ?? null,
                'rol' => 3,
                'created_at' => now(),
            ]);



            //Acepta política
            if ($request['accepted_terms'] == true) {
                // 2) Obtener la versión y hash actual de la política

                $policy = PolicyVersion::latest('id')->firstOrFail();

                // 3) Registrar el consentimiento, pasándole la IP
                $con = Consents::recordAcceptance(
                    $user->id,
                    $policy->policy_version,
                    $policy->policy_hash,
                    $request->ip()
                );
                $user->consent_id = $con->id;
                $user->save();

                return response()->json(['message' => 'Registro completado', 'user' => $user, 'status' => 201], 201);
            } else {
                return response()->json(['message' => 'Lo sentimos para utilizar esta aplicación es necesario que acepte los terminos'], 403);
            }



        } catch (ValidationException $e) {
            // Manejo específico de la excepción de validación
            $errors = $e->validator->errors();
            return response()->json(['errores' => $errors], 422);

        } catch (Exception $e) {
            // Manejo de cualquier otra excepción
            return response()->json([
                'message' => 'Error del servidor contacte con nosotros.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function aceptarNuevaPolitica(Request $request)
    {
        $user = auth()->user();
        // $u = User::find ($user->id);
        //Log::info('>>> Entró en aceptarNuevaPolitica');

        if (isset($request['accept_terms']) && $request['accept_terms'] == true) {
            // 2) Obtener la versión y hash actual de la política
            $policy = PolicyVersion::latest('id')->first();
            if (!$policy) {
                return response()->json(['message' => 'No hay política registrada'], 404);
            }

            // 3) Registrar el consentimiento, pasándole la IP
            $con = Consents::recordAcceptance(
                $user->id,
                $policy->policy_version,
                $policy->policy_hash,
                $request->ip()
            );

            return response()->json(['message' => 'Política aceptada', 'user' => $user, 'status' => 200], 200);
        } else {
            return response()->json(['message' => 'Debe aceptar la política para utilizar la aplición y podemos tratar sus datos'], 400);
        }

    }

    /**
     * Esto es un metodo de validación para comprobar que el usuario tienen un token valido y puede seguir usando la app de front-end
     *
     *  */
    public function validaUsuario()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['status' => 403, 'mensaje' => 'Token inválido o usuario no encontrado']);
        } else if (isset($user->consentimiento)) {
            return response()->json([
                'status' => 200,
                'mensaje' => 'Usuario válido',
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'mensaje' => 'Debe aceptar la politica de privacidad',
                'user' => $user,
            ], 400);
        }




    }
    // Eliminar cuenta (por el propio usuario)
    public function eliminarCuenta(Request $request)
    {
        try {
            $user = auth()->user();
            $user->fecha_baja = now();
            $user->save();
            return response()->json(['message' => 'Cuenta eliminada con exito'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 403, 'mensaje' => $e->getMessage()]);
        }
    }

    // Eliminar un usuario (solo por administradores)
    public function eliminarUsuario($id)
    {
        try {

            DB::beginTransaction();

            $user = User::findOrFail($id);

            // Paso 1: Eliminar todas las parcelas
            foreach ($user->parcelas as $parcela) {
                // Paso 2: Eliminar todos los cultivos de cada parcela
                foreach ($parcela->cultivos as $cultivo) {

                    // Eliminar relaciones pivot
                    $cultivo->fitosanitarios()->detach();

                    //eliminamos los pases de los aperos
                    $cultivo->pasesAperos()->delete();
                    // Eliminar subentidades (hasMany o hasOne)
                    $cultivo->fertilizantes()->delete();

                    // Finalmente, eliminar el cultivo
                    $cultivo->delete();
                }
                $parcela->filtros()->detach();
                // Eliminar la parcela
                $parcela->delete();
            }
            //busqueda de las máquinas y aperos del agricultor y borrado...
            // $user->maquinas->delete();
            // $user->aperos->delete();

            // Eliminar el usuario
            $user->delete();

            DB::commit();

            return response()->json(['message' => 'Usuario eliminado con exito'], 200);
        } catch (Exception $e) {

            DB::rollBack(); //Algo falló, deshacemos los cambios sobre la bd
            return response()->json(['status' => 503, 'mensaje' => $e->getMessage()], 503);
        }
    }

    // Cambiar contraseña (por el usuario)
    public function cambiarContrasen(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string|confirmed',
            ]);

            $user = auth()->user();

            if (get_class($user) == User::class) { //para asgurar que tenemos un objeto de la clase user obtenido de la uthenticación

                $user->password = Hash::make($request->password);
                //hay que configurar un helper con una interface que defina esos nombres para que intelephense no detecte esto como un error
                $user->save(); //no reconoce como un obejeto de usuario por eso Intelephense lo detecta como un error pero esta operación es válida si el usuario se authentica
            }
            return response()->json(['message' => 'Contraseña cambiada con exito'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 403, 'mensaje' => $e->getMessage()]);
        }
    }

    // Obtener todos los usuarios y, si son agricultores, incluir la información del agricultor
    public function obtenerUsuariosConAgricultores($idInicio = null)
    {
        try {
            // Si se pasa un ID de inicio, cargamos desde ese ID en adelante
            if ($idInicio) {
                $usuarios = User::with(['superior', 'dependientes'])
                    ->where('id', '>', $idInicio)
                    ->limit(100)
                    ->get();
            } else {
                // Si no se pasa ID, obtenemos todos los usuarios con sus relaciones
                $usuarios = User::with(['superior', 'dependientes'])->get();
            }

            return response()->json($usuarios, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Función para buscar usuarios dentro de la bd sin tenerlos cargados en la aplicación.
     */
    public function buscarUsuarios($termino = null)
    {
        // Crear la consulta para filtrar usuarios
        $query = User::query();

        if ($termino) {
            $query->where(function ($query) use ($termino) {
                $query->where('nombre', 'like', '%' . $termino . '%')
                    ->orWhere('dni', 'like', '%' . $termino . '%');
            });
        }


        $usuarios = $query->get();

        return response()->json($usuarios, 200);
    }
    // Editar perfil (por el usuario)
    public function editarUsuario(Request $request)
    {
        try {
            $u = auth()->user();

            // Validación de los campos que se pueden editar
            $validated = $request->validate([
                'nombre' => 'nullable|string|max:255',
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('users')->ignore($request['id'])
                ],
                'dni' => 'nullable|string|max:20',
                'telefono' => 'nullable|string|max:20',
                'rol' => 'nullable|integer|in:1,2,3', // 1=superadmin, 2=admin, 3=agricultor
                'user_depend' => 'nullable|exists:users,id', // <-- ya no usamos different:id
            ]);

            // Buscar el usuario a editar
            $usuario = User::findOrFail($request['id']);

            // Si el usuario es admin o superadmin, solo puede editarlo un superadmin
            if ($usuario->rol == 1 || $usuario->rol == 2) {
                $this->authorizeSuperAdmin();
            }

            // Actualizar campos básicos
            $usuario->nombre = $request['nombre'] ?? $usuario->nombre;
            $usuario->email = $request['email'] ?? $usuario->email;
            $usuario->dni = $request['dni'] ?? $usuario->dni;
            $usuario->telefono = $request['telefono'] ?? $usuario->telefono;

            // Actualizar rol solo si el que edita es superadmin
            if ($u->rol == 1) {
                $usuario->rol = $request['rol'] ?? $usuario->rol;
            }

            // Actualizar usuario superior (dependencia)
            if (isset($request['user_depend'])) {
                $usuario->user_depend = $request['user_depend'];
            }

            // Guardar cambios
            if ($usuario->save()) {
                return response()->json([
                    'message' => 'Perfil actualizado con éxito',
                    'usuario' => $usuario
                ], 200);
            }

            return response()->json(['message' => 'No se pudo actualizar el usuario'], 400);

        } catch (Exception $e) {
            return response()->json([
                'status' => 403,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
    //Este método es solo para que los agricultores se editen a si mismos
    public function editarUsuario2(Request $request)
    {
        try {
            $u = auth()->user();
            if ($u->id == $request['id']) { //comprobamos que solo el usuario se puede editar a si mismo


                $usuario = User::find($request['id']);
                $usuario->nombre = $request['nombre'] ?? $usuario->nombre;
                $usuario->email = $request['email'] ?? $usuario->email;
                $usuario->dni = $request['dni'] ?? $usuario->dni; //dni o CIF si viene si no dejamos el que estaba
                $usuario->telefono = $request['telefono'] ?? $usuario->telefono;
                $usuario->n_referencia = $request['n_referencia'] ?? $usuario->n_referencia;


                if ($usuario->save()) {
                    return response()->json(['message' => 'Perfil actualizado con exito', 'usuario' => $usuario], 200);
                }
            } else {
                return response()->json(['message' => 'No puede modificar un usuario ajeno si no es admin'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 403, 'mensaje' => $e->getMessage()]);
        }
    }
    // Metodo privado para verificar si el usuario es administrador
    public function authorizeAdmin()
    {
        try {
            // $user = auth()->user();
            $user = Auth::user();
            if ($user->rol !== 1 || $user->rol !== 2) {
                return response()->json(['error' => 'Acceso no autorizado'], 403);
            } else {
                return $user;
            }
        } catch (Exception $e) {
            return response()->json(['status' => 403, 'mensaje' => $e->getMessage()]);
        }
    }
    // Metodo privado para verificar si el usuario es administrador
    public function authorizeSuperAdmin()
    {
        try {
            // $user = auth()->user();
            $user = Auth::user();
            if ($user->rol !== 1) {
                return response()->json(['error' => 'No autorizado'], 403);
            } else {
                return $user;
            }
        } catch (Exception $e) {
            return response()->json(['status' => 403, 'mensaje' => $e->getMessage()]);
        }
    }

    public function recuperarContrasen(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email|exists:users,email']);

            $status = Password::sendResetLink(
                $request->only('email')
            );
            if ($status === Password::RESET_LINK_SENT) {

                return response()->json(['message' => 'Enlace de restablecimiento enviado a su correo electrónico.'], 200);
            }

        } catch (ValidationException $e) {
            LogController::errores("Errores de validacion " . $e->validator->errors());
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            LogController::errores("Error del servidor " . $e->getMessage());
            return response()->json(['message' => 'Hubo un error al enviar el enlace.'], 500);
        }


    }
    public function updatePassword(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Procesar el restablecimiento de la contraseña
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->away('https://acv.dtagro.es')->with('status', __($status)); //esta url habrá que cambiarla cuando estemos en prod
        }

        return back()->withErrors(['email' => [__($status)]]);
    }
    public function logout()
    {
        try {

            Auth::logout();
            // Invalidar el token del usuario actual
            JWTAuth::invalidate(JWTAuth::getToken());
            // $user->currentAccessToken()->delete();
            //dd($user);
            return response()->json(['mensaje' => 'Deslogueado correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'Error al intentar cerrar sesión', 'error' => $e->getMessage()], 500);
        }
    }
    public function obtenerUsuarioConDependientes($id)
    {
        // Cargamos el usuario junto con sus relaciones
        $usuario = User::with('superior', 'dependientes')->find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario, 200);
    }
}
