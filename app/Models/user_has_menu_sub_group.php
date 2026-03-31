<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_has_menu_sub_group extends Model
{
    protected $connection = 'mysql';
    protected $table = 'user_has_menu_sub_groups';
    // public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'role_id',
        'menu_sub_group_id',
        'allow_view',
        'allow_create',
        'allow_update',
        'allow_delete',
        'allow_print'
    ];
}
