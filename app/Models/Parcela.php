<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;





        protected $table = 'parcelas';
        public $timestamps = false;
        protected $fillable = [
            'id',
            'nombre',
            'id_agricultor',
            'numero_sigpac',
            'provincia',
            'municipio',
            'poligono',
            'parcela',
            'ref_catastral',
            'zona',
            'agregado',
            'recinto',
            'id_parcela_pp',
            'superficie',
            'ancho_caseta',
            'largo_caseta',
            'alto_caseta',
            'fecha_baja',
            'pais',
            'concelho',
            'distrito'
        ];

        public function agricultor()
        {
            return $this->belongsTo(User::class, 'id_agricultor');
        }

        public function provincia()
        {
            return $this->belongsTo(Provincia::class, 'provincia');
        }

        public function tanques()
        {
            return $this->hasMany(TanqueFertilizante::class, 'id_parcela');
        }

        public function municipio()
        {
            return $this->belongsTo(Municipio::class, 'municipio');
        }
        public function filtros()
        {
            return $this->belongsToMany(Filtro::class, 'filtros_parcela', 'id_parcela', 'id_filtro');
        }
        public function cultivos (){
            return $this->hasMany (CultivoParcela::class, 'id_parcela', 'id');
        }
}

