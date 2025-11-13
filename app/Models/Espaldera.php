<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espaldera extends Model
{

    protected $table = 'espalderas';

    protected $fillable = [
        'id',
        'nombre'
    ];

    public function cultivoParcelas()
    {
        return $this->hasMany(CultivoParcela::class, 'espaldera');
    }
}
