<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transaksi_tengki_detail extends Model
{
    //
    protected $connection = 'mysql';
    protected $table = 'transaksi_tengki_details';
    public $incrementing = true;
    protected $primaryKey = 'id';
    // protected $keyType = 'string';
    protected $fillable = [
        'id',
        'transaksi_tengki_id',
        'tanggal',
        'jumlah_ret',
        'harga',
        'total',
        'type',
        'keterangan',
    ];

    public function transaksi_tengki()
    {
        return $this->belongsTo(transaksi_tengki::class);
    }
}
