<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filtro extends Model
{
    use HasFactory;
  
        protected $table = 'filtros';
    
        protected $fillable = [
            'id',
            'nombre', 
            'kg_acero'
        ];
    
        public function parcelas()
        {
            return $this->belongsToMany(Parcela::class, 'filtros_parcela', 'id_filtro', 'id_parcela')
                        ->withPivot('fecha');
        }
}
    