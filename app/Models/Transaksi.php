<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'barang_id',
        'qty',
        'jenis_transaksi',
        'stok_sebelum',
        'stok_sesudah',
        'catatan'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
