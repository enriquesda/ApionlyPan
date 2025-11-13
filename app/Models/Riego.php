<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riego extends Model
{
    use HasFactory;
 
    protected $table = 'riegos';

    protected $fillable = [
        'id',
        'nombre'
    ];

    public function parcelas()
    {
        return $this->hasMany(Parcela::class, 'sistema_riego');
    }
}
