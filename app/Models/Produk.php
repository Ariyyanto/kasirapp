<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'jumlah_stok',
        'harga',
        'barcode',
    ];

    public function historis()
    {
        return $this->hasMany(Historis::class, 'id_produk');
    }

    public function prediksi()
    {
        return $this->hasMany(Prediksi::class, 'id_produk');
    }
}