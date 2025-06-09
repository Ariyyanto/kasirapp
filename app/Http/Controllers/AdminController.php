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
    $validated = $request->validate([
        'nama_produk' => 'required|string|max:255',
        'harga' => 'required|numeric|min:0',
        'jumlah_stok' => 'required|integer|min:0',
        'barcode' => 'nullable|string|max:100|unique:produk,barcode',
        'deskripsi' => 'nullable|string'
    ]);

    try {
        $produk = Produk::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $produk
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan produk',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'nama_produk' => 'required|string|max:255',
        'harga' => 'required|numeric|min:0',
        'jumlah_stok' => 'required|integer|min:0',
        'barcode' => 'nullable|string|max:100|unique:produk,barcode,'.$id,
        'deskripsi' => 'nullable|string'
    ]);

    try {
        $produk = Produk::findOrFail($id);
        $produk->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $produk
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui produk',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
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
        $perbandinganMetode = [];
        $rekomendasiMetode = null;

        $produk = Produk::all();
        return view('admin.prediksi.index', compact(
            'prediksi',
            'produkTerpilih',
            'detailPerhitungan',
            'perbandinganMetode',
            'rekomendasiMetode',
            'produk'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000'
        ]);

        $id_produk = $request->id_produk;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Get historical data
        $historis = $this->getHistoricalData($id_produk, $tahun, $bulan);
        
        if ($historis->count() < 6) {
            return redirect()->back()->with('error', 'Data historis kurang dari 6 bulan. Tidak dapat melakukan prediksi.');
        }

        // Calculate predictions
        $smaResults = $this->calculateSMA($historis);
        $wmaResults = $this->calculateWMA($historis);
        
        // Compare methods and recommend best one
        $rekomendasiMetode = $this->compareMethods($smaResults, $wmaResults);
        
        // Prepare data for view
        $produkTerpilih = Produk::find($id_produk);
        $detailPerhitungan = $this->prepareCalculationDetails($historis, $smaResults, $wmaResults);
        $perbandinganMetode = $this->prepareMethodComparison($smaResults, $wmaResults);
        
        return view('admin.prediksi.index', [
            'produk' => Produk::all(),
            'prediksi' => (object)[
                'sma' => $smaResults['prediction'],
                'wma' => $wmaResults['prediction'],
                'rekomendasi' => $rekomendasiMetode
            ],
            'produkTerpilih' => $produkTerpilih,
            'detailPerhitungan' => $detailPerhitungan,
            'perbandinganMetode' => $perbandinganMetode,
            'rekomendasiMetode' => $rekomendasiMetode
        ]);
    }

    private function getHistoricalData($id_produk, $tahun, $bulan)
    {
        $targetDate = Carbon::create($tahun, $bulan, 1);
        $startDate = $targetDate->copy()->subMonths(12);
        
        return Historis::where('id_produk', $id_produk)
            ->whereBetween('tanggal_penjualan', [$startDate, $targetDate->subMonth()])
            ->select(
                DB::raw('YEAR(tanggal_penjualan) as tahun'),
                DB::raw('MONTH(tanggal_penjualan) as bulan'),
                DB::raw('SUM(jumlah_jual) as jumlah_jual')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();
    }

    private function calculateSMA($historis)
    {
        $data = $historis->pluck('jumlah_jual')->toArray();
        $windowSize = min(6, count($data)); // Use 6-month window or available data
        
        $predictions = [];
        $errors = [];
        $absoluteErrors = [];
        $squaredErrors = [];
        
        // Calculate SMA for each possible window
        for ($i = $windowSize; $i < count($data); $i++) {
            $window = array_slice($data, $i - $windowSize, $windowSize);
            $sma = array_sum($window) / $windowSize;
            $actual = $data[$i];
            $error = $actual - $sma;
            
            $predictions[] = $sma;
            $errors[] = $error;
            $absoluteErrors[] = abs($error);
            $squaredErrors[] = pow($error, 2);
        }
        
        // Final prediction (average of last window)
        $finalPrediction = array_sum(array_slice($data, -$windowSize)) / $windowSize;
        
        return [
            'prediction' => round($finalPrediction),
            'mape' => $this->calculateMAPE($data, $predictions),
            'mse' => count($squaredErrors) > 0 ? array_sum($squaredErrors) / count($squaredErrors) : 0,
            'mae' => count($absoluteErrors) > 0 ? array_sum($absoluteErrors) / count($absoluteErrors) : 0,
            'window_size' => $windowSize,
            'predictions' => $predictions,
            'errors' => $errors
        ];
    }

    private function calculateWMA($historis)
    {
        $data = $historis->pluck('jumlah_jual')->toArray();
        $windowSize = min(6, count($data)); // Use 6-month window or available data
        
        $predictions = [];
        $errors = [];
        $absoluteErrors = [];
        $squaredErrors = [];
        
        // Calculate WMA for each possible window
        for ($i = $windowSize; $i < count($data); $i++) {
            $window = array_slice($data, $i - $windowSize, $windowSize);
            $weights = range(1, $windowSize);
            $totalWeight = array_sum($weights);
            
            $wma = 0;
            foreach ($window as $index => $value) {
                $wma += $value * $weights[$index];
            }
            $wma /= $totalWeight;
            
            $actual = $data[$i];
            $error = $actual - $wma;
            
            $predictions[] = $wma;
            $errors[] = $error;
            $absoluteErrors[] = abs($error);
            $squaredErrors[] = pow($error, 2);
        }
        
        // Final prediction (WMA of last window)
        $finalWindow = array_slice($data, -$windowSize);
        $weights = range(1, $windowSize);
        $totalWeight = array_sum($weights);
        
        $finalPrediction = 0;
        foreach ($finalWindow as $index => $value) {
            $finalPrediction += $value * $weights[$index];
        }
        $finalPrediction /= $totalWeight;
        
        return [
            'prediction' => round($finalPrediction),
            'mape' => $this->calculateMAPE($data, $predictions),
            'mse' => count($squaredErrors) > 0 ? array_sum($squaredErrors) / count($squaredErrors) : 0,
            'mae' => count($absoluteErrors) > 0 ? array_sum($absoluteErrors) / count($absoluteErrors) : 0,
            'window_size' => $windowSize,
            'weights' => $weights,
            'predictions' => $predictions,
            'errors' => $errors
        ];
    }

    private function calculateMAPE($actuals, $predictions)
    {
        $sumPercentage = 0;
        $count = min(count($actuals), count($predictions));
        
        for ($i = 0; $i < $count; $i++) {
            if ($actuals[$i] != 0) { // Avoid division by zero
                $sumPercentage += abs(($actuals[$i] - $predictions[$i]) / $actuals[$i]);
            }
        }
        
        return $count > 0 ? ($sumPercentage / $count) * 100 : 0;
    }

    private function compareMethods($sma, $wma)
    {
        // Compare based on MAPE (lower is better)
        if ($sma['mape'] < $wma['mape']) {
            return [
                'metode' => 'SMA',
                'alasan' => 'Memiliki MAPE lebih rendah (' . number_format($sma['mape'], 2) . '% vs ' . number_format($wma['mape'], 2) . '%)',
                'prediksi' => $sma['prediction']
            ];
        } else {
            return [
                'metode' => 'WMA',
                'alasan' => 'Memiliki MAPE lebih rendah (' . number_format($wma['mape'], 2) . '% vs ' . number_format($sma['mape'], 2) . '%)',
                'prediksi' => $wma['prediction']
            ];
        }
    }

    private function prepareCalculationDetails($historis, $sma, $wma)
    {
        $details = [];
        $data = $historis->pluck('jumlah_jual')->toArray();
        $windowSize = $sma['window_size'];
        
        for ($i = $windowSize; $i < count($data); $i++) {
            $date = $historis[$i];
            $bulan = Carbon::create($date->tahun, $date->bulan, 1)->translatedFormat('F Y');
            
            $smaIndex = $i - $windowSize;
            $wmaIndex = $i - $windowSize;
            
            $details[] = [
                'bulan' => $bulan,
                'aktual' => $data[$i],
                'sma' => isset($sma['predictions'][$smaIndex]) ? round($sma['predictions'][$smaIndex], 2) : '-',
                'wma' => isset($wma['predictions'][$wmaIndex]) ? round($wma['predictions'][$wmaIndex], 2) : '-',
                'error_sma' => isset($sma['errors'][$smaIndex]) ? round($sma['errors'][$smaIndex], 2) : '-',
                'error_wma' => isset($wma['errors'][$wmaIndex]) ? round($wma['errors'][$wmaIndex], 2) : '-',
            ];
        }
        
        return $details;
    }

    private function prepareMethodComparison($sma, $wma)
    {
        return [
            'SMA' => [
                'window_size' => $sma['window_size'],
                'mape' => number_format($sma['mape'], 2) . '%',
                'mse' => number_format($sma['mse'], 2),
                'mae' => number_format($sma['mae'], 2),
                'rumus' => 'SMA = (D₁ + D₂ + ... + Dₙ) / n'
            ],
            'WMA' => [
                'window_size' => $wma['window_size'],
                'weights' => implode(', ', $wma['weights']),
                'mape' => number_format($wma['mape'], 2) . '%',
                'mse' => number_format($wma['mse'], 2),
                'mae' => number_format($wma['mae'], 2),
                'rumus' => 'WMA = (1×D₁ + 2×D₂ + ... + n×Dₙ) / (1+2+...+n)'
            ]
        ];
    }


}