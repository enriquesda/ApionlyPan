<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cultivo extends Model
{
    use HasFactory;
        protected $table = 'cultivos';
    
        protected $fillable = [
            'nombre', 
            
        ];
    
        public function cultivoParcelas()
        {
            return $this->hasMany(CultivoParcela::class, 'id_cultivo');
        }
}
    