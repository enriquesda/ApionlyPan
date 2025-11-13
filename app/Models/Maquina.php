<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquina extends Model
{
    use HasFactory;

        protected $table = 'maquinaria';
        public $timestamps = false;
        protected $fillable = [
            'id',
            'tipo_cultivo', 
            'nombre', 
            'cv', 
            'kw', 
            'consumo_l_h', 
            'rendimiento_h_ha', 
            'consumo_l_ha', 
            'vida_h', 
            'peso', 
            'fabricacion', 
            'reparacion',
            'img',
        ];
    
        public function agricultores()
        {
            return $this->belongsToMany(User::class, 'maquinaria_agricultor', 'id_maquina', 'id_agricultor');
        }
    
        public function parcelas()
        {
            return $this->belongsToMany(Parcela::class, 'maquinaria_cultivo', 'id_maquina', 'id_cultivo_parcela')
                        ->withPivot('horas', 'fecha');
        }
}
    