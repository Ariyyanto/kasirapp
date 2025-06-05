<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediksi extends Model
{
    use HasFactory;

    protected $table = 'prediksi';
    
    protected $fillable = [
        'id_produk',
        'tanggal_prediksi',
        'jumlah_prediksi',
        'mape',
        'mse'
    ];
    
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
    
    protected $dates = ['tanggal_prediksi'];
}