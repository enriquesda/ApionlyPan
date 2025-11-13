<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiltroParcela extends Model
{
    use HasFactory;
  
        protected $table = 'filtros_parcela';
    
			

        protected $fillable = [
            'id_filtro', 
            'id_parcela',
            'fecha'
        ];
    
     
}
    