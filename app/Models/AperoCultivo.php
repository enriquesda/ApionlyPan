<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AperoCultivo extends Model
{
    use HasFactory;

    protected $table = 'aperos_cultivo';
    public $timestamps = false;
    public $incrementing = false; // Importante para claves compuestas
    protected $primaryKey = ['id_cultivo_parcela', 'id_apero']; // Clave compuesta
    protected $keyType = 'int';

    protected $fillable = [
        'id_apero',
        'id_cultivo_parcela',
        'id_maquina',
        'pases',
        'fecha',
    ];

    /**
     * Override para manejar claves primarias compuestas
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Override para obtener el valor de la clave
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    // Relación con la tabla Maquina
    public function maquina() {
        return $this->belongsTo(Maquina::class, 'id_maquina');
    }

    // Relación con la tabla Apero
    public function apero() {
        return $this->belongsTo(Apero::class, 'id_apero');
    }

    public function cultivoParcela() {
        return $this->belongsTo(CultivoParcela::class, 'id_cultivo_parcela', 'id');
    }
}
