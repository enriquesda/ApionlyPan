<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoBombeo extends Model
{
    use HasFactory;
  
        protected $table = 'grupos_bombeo';
    
        protected $fillable = [
            'id',
            'potencia_bomba', 
            'cabeas', 
            'diesel_l_h', 
            'energia_kw_h', 
            'peso_kg', 
            'vida', 
            'kg_acero'
        ];
    
        public function parcelas()
        {
            return $this->hasMany(Parcela::class, 'bomba');
        }
}
    
