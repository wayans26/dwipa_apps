<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class menu_sub_group extends Model
{
    protected $connection = 'mysql';
    protected $table = 'menu_sub_groups';
    // public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'page_name',
        'order_no',
        'menu_group_id',
    ];
}
