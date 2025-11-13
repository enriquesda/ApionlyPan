<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable implements JWTSubject, CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable;



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nombre',
        'email',
        'token',
        'dni',
        'telefono',
        'n_referencia',
        'rol',
        'password',
        'user_depend',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * Obtener el identificador que será almacenado en el JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function consentimiento()
    {
        return $this->hasOne(Consents::class, 'id_user', 'id');
    }
    /**
     * Obtener cualquier reclamo personalizado que será agregado al JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function agricultor()
    {
        return $this->hasOne(Agricultor::class, 'id_usuario', 'id');
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    public function esAdmin()
    {
        // 1 = Super Admin, 2 = Admin normal
        return in_array($this->rol, [1, 2]);
    }

    public function esSuperAdmin()
    {
        return $this->rol === 1;
    }

    public function esPropietario($id)
    {
        // Comprueba si el id coincide con el del usuario para ver si es propietarios
        return $id == $this->id;
    }
    public function esPropietarioDe($recurso)
    {

        if ($this->esAdmin())
            return true;

        // dd($recurso->id_agricultor.$this->id);
        if (isset($recurso['id_agricultor'])) {
            if ($recurso['id_agricultor'] == $this->id) {

                return true;
            } else {
                return false;
            }
        }
        // Acceso directo a la parcela
        if (method_exists($recurso, 'parcela')) {

            $recurso->loadMissing('parcela');

            if ($recurso->parcela && $recurso->parcela->id_agricultor === $this->id) {
                return true;
            } else {
                return false;
            }
        }

        // Acceso indirecto: recurso → cultivoParcela → parcela
        if (method_exists($recurso, 'cultivoParcela')) {

            $recurso->loadMissing('cultivoParcela.parcela');

            $cultivoParcela = $recurso->cultivoParcela;
            $parcela = $cultivoParcela->parcela;

            if ($parcela && $parcela->id_agricultor === $this->id) {
                return true;
            }
        }

        return false;
    }

    function parcelas()
    {
        return $this->hasMany(Parcela::class, 'id_agricultor', 'id');
    }
    function maquinas()
    {
        return $this->hasMany(Maquina::class, 'id_agricultor', 'id');
    }
    function aperos()
    {
        return $this->hasMany(Apero::class, 'id_agricultor', 'id');
    }
    public function aperosAgricultor()
    {
        return $this->hasMany(AperoAgricultor::class, 'id_agricultor');
    }
    // Usuario del que depende este usuario
    public function superior()
    {
        return $this->belongsTo(User::class, 'user_depend');
    }

    // Usuarios que dependen de este usuario
    public function dependientes()
    {
        return $this->hasMany(User::class, 'user_depend');
    }

}
