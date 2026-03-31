<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    // public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'username',
        'full_name',
        'phone',
        'email',
        'password',
        'role_id',
        'status',
        'image',
        'allow_mobile',
    ];

    protected $hidden = [
        'password',
    ];
}
