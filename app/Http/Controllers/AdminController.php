<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Historis;
use App\Models\Prediksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalProduk = Produk::count();
        $totalPenjualan = Historis::sum('total_harga');
        
        // Get best selling product
        $produkTerlaris = Historis::selectRaw('id_produk, sum(jumlah_jual) as total')
            ->groupBy('id_produk')
            ->orderBy('total', 'desc')
            ->with('produk')
            ->first();

        // Determine date range based on filter
        $filter = $request->input('filter', '7days');
        
        switch($filter) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case '7days':
                $startDate = Carbon::now()->subDays(6);
                $endDate = Carbon::now();
                break;
            case '30days':
                $startDate = Carbon::now()->subDays(29);
                $endDate = Carbon::now();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            default:
                $startDate = Carbon::now()->subDays(6);
                $endDate = Carbon::now();
        }

        // Get sales data for selected period
        $penjualanHarian = Historis::select(
                DB::raw('DATE(tanggal_penjualan) as tanggal'),
                DB::raw('SUM(total_harga) as total')
            )
            ->whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
        
        // Format data for chart
        $labels = [];
        $data = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dayName = $currentDate->isoFormat('dddd');
            
            $penjualan = $penjualanHarian->firstWhere('tanggal', $dateString);
            
            $labels[] = $dayName;
            $data[] = $penjualan ? $penjualan->total : 0;
            
            $currentDate->addDay();
        }

        // Get recent activities (last 5)
        $recentActivities = Historis::with('produk')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'penjualan',
                    'message' => 'Penjualan '.$item->produk->nama_produk.' ('.$item->jumlah_jual.' pcs)',
                    'time' => $item->created_at->diffForHumans(),
                    'icon' => 'fas fa-cart-plus',
                    'color' => 'primary'
                ];
            });

        // Add stock updates to activities
        $stockUpdates = Produk::whereNotNull('updated_at')
            ->where('updated_at', '!=', 'created_at')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'stok',
                    'message' => 'Stok '.$item->nama_produk.' diperbarui ('.$item->jumlah_stok.' pcs)',
                    'time' => $item->updated_at->diffForHumans(),
                    'icon' => 'fas fa-boxes',
                    'color' => 'success'
                ];
            });

        $activities = $recentActivities->merge($stockUpdates)
            ->sortByDesc(function($item) {
                return $item['time'];
            })
            ->take(5);

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalPenjualan',
            'produkTerlaris',
            'labels',
            'data',
            'activities',
            'filter'
        ));
    }

    public function index()
    {
        $produk = Produk::all();
        return view('admin.produk.index', compact('produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'harga' => 'required|numeric',
            'jumlah_stok' => 'required|integer',
            'barcode' => 'nullable|unique:produk,barcode'
        ]);

        Produk::create($request->all());
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required',
            'harga' => 'required|numeric',
            'jumlah_stok' => 'required|integer',
            'barcode' => 'nullable|unique:produk,barcode,'.$id
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update($request->all());
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }

    public function historis()
    {
        $historis = Historis::with('produk')->orderBy('tanggal_penjualan', 'desc')->get();
        return view('admin.historis.index', compact('historis'));
    }

    public function prediksi()
    {
        $prediksi = null;
        $produkTerpilih = null;
        $detailPerhitungan = [];

        $produk = Produk::all();
        return view('admin.prediksi.index', compact('prediksi', 'produkTerpilih', 'detailPerhitungan', 'produk'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000'
        ]);

        // Logika prediksi sama seperti sebelumnya
        $prediksi = $this->hitungPrediksi($request->id_produk, $request->bulan, $request->tahun);
        
        $produkTerpilih = Produk::find($request->id_produk);
        $detailPerhitungan = $this->getDetailPerhitungan($request->id_produk, $request->bulan, $request->tahun);
        
        return view('admin.prediksi.index', [
            'produk' => Produk::all(),
            'prediksi' => $prediksi,
            'produkTerpilih' => $produkTerpilih,
            'detailPerhitungan' => $detailPerhitungan
        ]);
    }

        private function hitungPrediksi($id_produk, $bulan, $tahun)
    {
        // Ambil 6 bulan terakhir dari produk terkait
        $historis = Historis::where('id_produk', $id_produk)
            ->whereYear('tanggal_penjualan', '<=', $tahun)
            ->orderBy('tanggal_penjualan', 'desc')
            ->limit(6)
            ->get()
            ->sortBy('tanggal_penjualan')
            ->values();

        if ($historis->count() < 3) {
            return null; // Tidak cukup data untuk prediksi
        }

        $sma = $historis->pluck('jumlah_jual')->avg();

        // Hitung WMA dengan bobot dari 1 ke 6
        $weights = [1, 2, 3, 4, 5, 6];
        $jumlah = $historis->pluck('jumlah_jual')->values();
        $totalWeight = array_sum($weights);

        $wma = 0;
        for ($i = 0; $i < count($jumlah); $i++) {
            $wma += $jumlah[$i] * $weights[$i];
        }
        $wma /= $totalWeight;

        return (object)[
            'jumlah_prediksi' => round($sma),
            'jumlah_prediksi_wma' => round($wma),
            'mape' => 5.6, // Dummy MAPE
            'mse' => 34.2,  // Dummy MSE
            'mape_wma' => 4.8,
            'mse_wma' => 30.1
        ];
    }

    private function getDetailPerhitungan($id_produk, $bulan, $tahun)
    {
        $historis = Historis::where('id_produk', $id_produk)
            ->whereYear('tanggal_penjualan', '<=', $tahun)
            ->orderBy('tanggal_penjualan', 'desc')
            ->limit(6)
            ->get()
            ->sortBy('tanggal_penjualan')
            ->values();

        $detail = [];
        $jumlah = $historis->pluck('jumlah_jual')->values();

        // Dummy perhitungan
        for ($i = 0; $i < $jumlah->count(); $i++) {
            $detail[] = [
                'bulan' => Carbon::parse($historis[$i]->tanggal_penjualan)->translatedFormat('F Y'),
                'aktual' => $jumlah[$i],
                'sma' => $jumlah->avg(),
                'wma' => $jumlah[$i], // Ganti sesuai rumus asli
                'error_sma' => abs($jumlah[$i] - $jumlah->avg()),
                'error_wma' => abs($jumlah[$i] - $jumlah[$i]),
            ];
        }

        return $detail;
    }

}