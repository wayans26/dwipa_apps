<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class personal_token extends Model
{
    protected $connection = 'mysql';
    protected $table = 'personal_tokens';
    // public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'iduser',
        'token',
        'device',
    ];
}
