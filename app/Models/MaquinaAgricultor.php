<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaquinaAgricultor extends Model
{
    use HasFactory;
    protected $table = 'maquinaria_agricultor';
    public $timestamps = false;
    public $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_agricultor',
        'id_maquina', 
        'id_sensor', 
        'device', 
       
    ];

    public function agricultores()
    {
       return $this->belongsTo(User::class, 'id_agricultor', 'id');
    }
    public function maquina(){
        return $this->belongsTo(Maquina::class, 'id_maquina', 'id');
    }
}
