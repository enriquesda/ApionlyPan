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


}
