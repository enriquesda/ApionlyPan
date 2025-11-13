<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FertilizanteModel extends Model
{
    use HasFactory;
    //Represena un feritlizante modelo con nombre con unas características determinadas que los agricultores podrán seleccionar como el que usan y el cual ya viene determinado con unos porcentajes
    //tanto de nitrogeno como de potasio cmo de fosforo
    protected $table = 'fertilizantes'; //esta tabla se llama igual que el modelo Fertilizantres que utilizabamos inicialmente como computo global de aplicación de fertilziantes en un cultivo... 
    //Ahora a 05 de Mayo de 2025 se ha rediseñado la forma de registrar las aplicaciones de fitosanitario aunque seguirá habiendo un computo global en el formulario del cultivo por lo cual
    //no se va a eliminar de momento el modelo Fertilizantes que usaba la tabla fertilizantes_cultivo. Se volcarán a partir de ahora las aplicaciones de fertilizantes en la tabla aplicaciones_fertilizante
    //y se hara la suma de las materias P K y N para ver el computo global en función de las aplicaciones 
    public $timestamps = false;
    protected $fillable = ['nombre', 'porcentaje_P', 'porcentaje_K', 'porcentaje_N'];
}
