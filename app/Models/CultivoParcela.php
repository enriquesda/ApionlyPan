<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CultivoParcela extends Model
{
    use HasFactory;

    protected $table = 'cultivo_parcela';

    protected $fillable = [
        'id',
        'id_parcela',
        'id_cultivo',
        'created_at',
        'updated_at',
        'fecha_baja',
        'fecha_siembra',
        'fecha_recoleccion',
        'espaldera',
        'n_pisos',
        'sistema_riego',
        'n_goteros_arbol',
        'n_sectores',
        'id_sector_pp',
        'caudal_gotero',
        'distancia_fitosanitarios',
        'distancia_transporte_tr',
        'distancia_transporte_c',
        'combustible_bomba',
        'bomba',
        'entre_arboles',
        'entre_calles',
        'superficie_cultivada',
        'agua',
        'dias_ciclo',
        'cosecha', //esta es una variable booleana que ventra a true si la cosecha la hace con maquinaria o a false (0) si la hace a mano como en la viña o en el olivo
        'produccion_t_ha',
        'informe_impacto', //archivo BIGblob -- no debe cargarse hasta que no obtenemos por id el cultivo
    ];

    protected $hidden = ['informe_impacto'];
    // Este campo virtual se podrá acceder como $cultivo->tiene_informe_impacto
    protected $appends = ['tiene_informe_impacto'];

    public function getTieneInformeImpactoAttribute(): bool
    {
        // Si no está cargado y no viene de la BD, lo consultamos en una query rápida
        if (!array_key_exists('informe_impacto', $this->attributes)) {
            return self::where('id', $this->id)->whereNotNull('informe_impacto')->exists();
        }

        return !is_null($this->attributes['informe_impacto']);
    }
    // Relaciones directas con otros modelos
    public function parcela()
    {
        return $this->belongsTo(Parcela::class, 'id_parcela');
    }

    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class, 'id_cultivo', 'id');
    }

    public function espaldera()
    {
        return $this->belongsTo(Espaldera::class, 'espaldera');
    }

    public function sistemaRiego()
    {
        return $this->belongsTo(Riego::class, 'sistema_riego');
    }

    public function grupoBombeo()
    {
        return $this->belongsTo(GrupoBombeo::class, 'bomba');
    }

    // Relaciones muchos a muchos
    public function fertilizantes()
    {
        return $this->hasOne(Fertilizante::class, 'id_cultivo_parcela', 'id');
        //return $this->hasMany(AplicacionFertilizante::class, 'id_cultivo', 'id');

    }
    public function aplicacionesFertilizantes()
    {
        return $this->hasMany(AplicacionFertilizante::class, 'id_cultivo', 'id');

    }

    public function fitosanitarios()
    {
        return $this->belongsToMany(Fitosanitario::class, 'fitosanitarios_cultivo', 'id_cultivo_parcela', 'id_fitosanitario');
        //->withPivot(['n_aplicaciones','fecha'] ); // Ajusta 'dosis' según el campo en la tabla pivote
    }

    public function pasesAperos()
    {
        return $this->hasMany(AperoCultivo::class, 'id_cultivo_parcela', 'id');
    }

    public function aperos()
    {
        return $this->belongsToMany(Apero::class, 'aperos_cultivo', 'id_cultivo_parcela', 'id_apero')
            ->withPivot(['pases', 'fecha']);
    }
    // public function aperos()
    // {
    //     return $this->hasMany(AperoCultivo::class,'id_cultivo_parcela' ,'id');
    //        // ->withPivot('pases', 'fecha');
    // }

    public function maquinas()
    {
        return $this->belongsToMany(Maquina::class, 'maquinaria_cultivo', 'id_cultivo_parcela', 'id_maquina');//->withPivot(['horas', 'fecha']);

        // return $this->hasMany(MaquinaCultivo::class, 'id_cultivo_parcela', 'id');
    }
    public function tanqueFertilizante()
    {
        return $this->hasMany(TanqueFertilizante::class, 'id_parcela', 'id_parcela');
    }


    public function aperosDelAgricultor()
    {
        // Asegúrate de cargar la parcela y el agricultor
        $parcela = $this->parcela()->first();
        if (!$parcela || !$parcela->id_agricultor) {
            return collect(); // colección vacía
        }

        $idAgricultor = $parcela->id_agricultor;

        // Retorna la colección de aperos filtrando por agricultor
        return AperoAgricultor::with('apero')->whereHas('agricultor', function ($query) use ($idAgricultor) {
            $query->where('id_agricultor', $idAgricultor);
        })
        ->get()
        ->pluck('apero') // Extrae solo los objetos Apero
        ->filter();
    }
    public function maquinasDelAgricultor()
    {
        // Asegúrate de cargar la parcela y el agricultor
        $parcela = $this->parcela()->first();
        if (!$parcela || !$parcela->id_agricultor) {
            return collect(); // colección vacía
        }

        $idAgricultor = $parcela->id_agricultor;

        // Retorna la colección de máquinas filtrando por agricultor
        return MaquinaAgricultor::with('maquina')->whereHas('agricultores', function ($query) use ($idAgricultor) {
            $query->where('id_agricultor', $idAgricultor);
        })
        ->get()
        ->pluck('maquina') // Extrae solo los objetos Maquina
        ->filter();
    }
}
