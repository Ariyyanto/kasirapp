<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Produk;
use Carbon\Carbon;

class HistorisSeeder extends Seeder
{
    public function run(): void
    {
        $produkList = Produk::all();

        foreach ($produkList as $produk) {
            $tanggalAwal = Carbon::parse('2024-06-01'); // 12 bulan ke belakang

            for ($i = 0; $i < 12; $i++) {
                $tanggal = $tanggalAwal->copy()->addMonths($i);
                $jumlah_jual = rand(10, 50);
                $harga = $produk->harga;
                $total_harga = $harga * $jumlah_jual;
                $stok_saat_ini = rand(20, 100);

                DB::table('historis')->insert([
                    'id_produk' => $produk->id,
                    'tanggal_penjualan' => $tanggal->format('Y-m-d'),
                    'jumlah_jual' => $jumlah_jual,
                    'jumlah_stok' => $stok_saat_ini,
                    'total_harga' => $total_harga,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
