<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class TanqueFertilizante extends Model
{
    use HasFactory;
    protected $table = 'tanque_fertilizantre';
    public $timestamps = false;

    protected $fillable = [
        'id_sensor',
        'alto',
        'ancho',
        'largo',
        'volumen',
        'porcentaje_P',
        'porcentaje_N',
        'porcentaje_K',
        'nombre_fert',
        'id_parcela'
    ];

    public function parcela()
    {
        return $this->belongsTo(Parcela::class, 'id_parcela');
    }
    public function lecturasSensor()
    {
        $client = new \GuzzleHttp\Client();
        // $url = 'https://api.sensores.com/lecturas/' . $this->id_sensor; 

        // try {
        //     $response = $client->request('GET', $url);
        //     $data = json_decode($response->getBody(), true);
        //     return $data;
        // } catch (\Exception $e) {
        //     // Manejo de errores, puedes personalizar esto
        //     return [
        //         'error' => true,
        //         'message' => $e->getMessage(),
        //     ];
        // }
        return []; //de momento lo dejamo vacio hasta que ns de info la uex
    }
}
