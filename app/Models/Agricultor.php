<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Agricultor extends Model
{
    use HasFactory;
    protected $table = 'agricultores';

    protected $fillable = [
        'id',
        'id_usuario', 
        'referencia_junta', 
        'telefono', 
        'created_at', 
        'updated_at', 
        'fecha_baja'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function aperos()
    {
        return $this->belongsToMany(Apero::class, 'aperos_agricultor', 'id_agricultor', 'id_apero');
    }

    public function parcelas()
    {
        return $this->hasMany(Parcela::class, 'id_agricultor');
    }

    public function maquinaria()
    {
        return $this->belongsToMany(Maquina::class, 'maquinaria_agricultor', 'id_agricultor', 'id_maquina');
    }
}
