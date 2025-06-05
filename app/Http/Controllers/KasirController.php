<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Historis;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir.index');
    }

    public function scan(Request $request)
    {
        $barcode = $request->input('barcode');
        $produk = Produk::where('barcode', $barcode)->first();

        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($produk);
    }

    public function checkout(Request $request)
    {
        $items = $request->input('items');

        foreach ($items as $item) {
            $produk = Produk::find($item['id']);
            
            if ($produk) {
                // Kurangi stok
                $produk->jumlah_stok -= $item['quantity'];
                $produk->save();

                // Catat di histori
                Historis::create([
                    'id_produk' => $produk->id,
                    'tanggal_penjualan' => now()->format('Y-m-d'),
                    'jumlah_jual' => $item['quantity'],
                    'jumlah_stok' => $produk->jumlah_stok,
                    'total_harga' => $item['quantity'] * $produk->harga,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function getProductByBarcode($barcode)
    {
        $produk = Produk::where('barcode', $barcode)->first();

        if ($produk) {
            return response()->json([
                'success' => true,
                'data' => $produk
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan'
        ], 404);
    }
}