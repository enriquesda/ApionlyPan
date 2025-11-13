<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fitosanitario extends Model
{
    use HasFactory;

    protected $table = 'fitosanitarios';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'materia_activa',
        'clasificacion_simapro',
        'porcentaje_ma',
        'densidad',
        'tipo',  //1,2,3,4,5,6,7 hervicida, un plaguicida, bactericida, fungicida, acaricida, rodenticida , otro¡
        'presion_vapor',
        'max_tomate_L_ha',
        'max_tomate_ha',
        'max_arroz_L_ha',
        'max_arroz_ha',
        'max_frutal_L_ha',
        'max_frutal_ha',
        'max_vid_L_ha',
        'max_ha_vid',
        'max_olivo_L_ha',
        'max_olivo_ha'
    ];
    
    public function parcelas()
    {
        return $this->belongsToMany(Parcela::class, 'fitosanitarios_cultivo', 'id_fitosanitario', 'id_cultivo_parcela')->withPivot('n_aplicaciones', 'fecha');  // Aquí accedemos a los datos adicionales en la tabla pivote;
    }
}
