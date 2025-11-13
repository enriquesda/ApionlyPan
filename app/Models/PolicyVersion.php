<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyVersion extends Model
{
    use HasFactory;
    protected $table = 'policy_current';
    protected $fillable = ['policy_url', 'policy_version', 'policy_hash', 'updated_at', 'created_at'];
}
