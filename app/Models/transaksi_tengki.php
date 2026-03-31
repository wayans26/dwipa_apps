<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transaksi_tengki extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'transaksi_tengkis';
    public $incrementing = true;
    protected $primaryKey = 'id';
    // protected $keyType = 'string';
    protected $fillable = [
        'periode',
        'status',
    ];

    public function transaksi_tengki_detail()
    {
        return $this->hasMany(transaksi_tengki_detail::class);
    }
}
