<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apero extends Model
{
    use HasFactory;
    protected $table = 'aperos';

    protected $fillable = [
        'id',
        'nombre',
        'labores_simapro',
        'tiempo_m_labor',
        'rendimiento_simapro',
        'unidad',
        'vida',
        'peso',
        'consumo',
        'created_at',
        'updated_at',
        'img', // Si se usa una imagen asociada
    ];

    // public function agricultores()
    // {
    //     return $this->belongsToMany(Agricultor::class, 'aperos_agricultor', 'id_apero', 'id_agricultor');
    // }

    // public function cultivo()
    // {
    //     return $this->belongsToMany(Parcela::class, 'aperos_cultivo', 'id_apero', 'id_cultivo_parcela')
    //                 ->withPivot('fecha', 'pases');
    // }

    public function aperosAgricultor()
    {
        return $this->hasMany(AperoAgricultor::class, 'id_apero');
    }
}


