<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consents extends Model
{
    use HasFactory;
    protected $table = 'consents';

    protected $primaryKey = 'id';

    public $timestamps = false; 
    // Usaremos campos propios en lugar de created_at / updated_at automáticos

    protected $fillable = [
        'id_user',
        'policy_version',
        'fecha_aceptacion',
        'fecha_revocado',
        'ip_user',
        'hash',
    ];

    protected $casts = [
        'fecha_aceptacion' => 'datetime',
        'fecha_revocado'   => 'datetime',
    ];
     /**
     * Registra un nuevo consentimiento para el usuario dado.
     *
     * @param  int    $userId
     * @param  string $policyVersion
     * @param  string $policyHash
     * @return self
     */
    public static function recordAcceptance(int $userId, string $policyVersion, string $policyHash): self
    {
        $p = self::where(['id_user'=>$userId, 'policy_version' => $policyVersion] )->first();
       
        if (isset($p)){
            return $p;
        }

        return self::create([
            'id_user'          => $userId,
            'policy_version'   => $policyVersion,
            'fecha_aceptacion' => now(),
            'fecha_revocado'   => null,
            'ip_user'          => request()->ip(),
            'hash'             => $policyHash,
        ]);
    }
}
