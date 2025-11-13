<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;
    protected $table = 'provincias';
    private $tiemestamps = false;
    protected $fillable = [
        'id',
        'nombre'
    ];

    public function provincia()
    {
        return $this->hasMany(Municipio::class, 'id_provincia', 'id');
    }
}
