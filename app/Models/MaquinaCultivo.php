<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaquinaCultivo extends Model
{
    use HasFactory;


   
        protected $table = 'maquinaria_cultivo';
        public $timestamps = false;
        protected $fillable = [
            'id_cultivo_parcela',
            'id_maquina', 
            'horas', 
            'fecha'
        ];
    
        public function maquina() {
            return $this->belongsTo (Maquina::class, 'id_maquina', 'id');
        }
        public function cultivoParcela (){
            return $this->belongsTo (CultivoParcela::class, 'id_cultivo_parcela', 'id');
        }
}
    