<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AplicacionFertilizante extends Model
{
    use HasFactory;
    protected $table = 'aplicaciones_fertilizantes';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_cultivo',
        'id_agricultor',
        'nombre_fert',
        'unidades_N',
        'unidades_P',
        'unidades_K',
        'porcentaje_N',
        'porcentaje_P',
        'porcentaje_K',
        'kg_ha',
        'km',
        'fecha',
        'id_lectura_sensor'
    ];
    
    public function cultivo () {
        return $this->belongsTo (CultivoParcela::class, 'id_cultivo');
    }
    public function agricultor() {
        return $this->belongsTo(User::class, 'id_agricultor', 'id');
    }
}
