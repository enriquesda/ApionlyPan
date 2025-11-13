<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fertilizante extends Model
{
    use HasFactory;

    protected $table = 'fertilizantes_cultivo';
    public $timestamps = false;
    protected $primaryKey = 'id_cultivo_parcela';
    protected $fillable = [
        'id_cultivo_parcela',
        'nombre_fertilizante',
        'uds_N',
        'uds_P',
        'uds_K',
        'porcentaje_N',
        'porcentaje_P',
        'porcentaje_K',
        'kg_ha',
        'km',
        'fecha'
    ];
    
    public function cultivo () {
        return $this->belongsTo (CultivoParcela::class, 'id_cultivo_parcela');
    }
}
