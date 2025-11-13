<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperoAgricultor extends Model
{
    use HasFactory;

    protected $table = 'aperos_agricultor';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_apero',
        'id_agricultor',
        'id_sensor', // Si se usa un sensor específico
        'device', // Dispositivo asociado, si aplica
       
    ];

    // Relación con el modelo Apero
    public function apero()
    {
        return $this->belongsTo(Apero::class, 'id_apero');
    }

    // Relación con el modelo User (Agricultor)
    public function agricultor()
    {
        return $this->belongsTo(User::class, 'id_agricultor');
    }

    // Relación con los aperos_cultivo
    public function aperosCultivo()
    {
        return $this->hasMany(AperoCultivo::class, 'id_apero', 'id_apero');
    }
}
