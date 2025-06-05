<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historis extends Model
{
    use HasFactory;

    protected $table = 'historis';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_produk',
        'tanggal_penjualan',
        'jumlah_jual',
        'jumlah_stok',
        'total_harga',
    ];

    protected $dates = ['tanggal_penjualan'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}