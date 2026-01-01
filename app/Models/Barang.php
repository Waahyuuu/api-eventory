<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    
    protected $table = 'barang';

    protected $fillable = [
        'namaBarang',
        'kategori',
        'stok',
        'deskripsi',
        'images',
    ];
        public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'barang_id');
    }
}
