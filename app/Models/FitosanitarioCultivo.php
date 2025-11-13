<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitosanitarioCultivo extends Model
{
    use HasFactory;

    protected $table = 'fitosanitarios_cultivo';

    protected $fillable = [
        'id_cultivo_parcela',
        'id_fitosanitario',
        'n_aplicaciones',
        'fecha', 
    ];

 
    public function cultivoParcela() {
        return $this->belongsTo(CultivoParcela::class, 'id_cultivo_parcela', 'id');
    }
    /**
     * Devuelve la información completa del fitosanitario
     */
    public function fitosanitario(){
        return $this->belongsTo(Fitosanitario::class, 'id_fitosanitario', 'id');
    }
}
