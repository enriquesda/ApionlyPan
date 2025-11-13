<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;
    protected $table = 'municipios';
    private $tiemestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'id_provincia'
    ];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia');
    }
}
