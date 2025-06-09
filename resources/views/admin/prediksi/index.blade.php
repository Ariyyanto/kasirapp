@extends('layouts.app')

@section('title', 'Prediksi Stok Barang')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Prediksi Stok Barang</h5>
        </div>
        <div class="card-body">
            <!-- Form Prediksi -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form id="prediksiForm" method="POST" action="{{ route('prediksi.generate') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="id_produk" class="form-label">Pilih Produk</label>
                                <select class="form-select" id="id_produk" name="id_produk" required>
                                    @foreach($produk as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode_produk }} - {{ $item->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="bulan" class="form-label">Bulan</label>
                                <select class="form-select" id="bulan" name="bulan" required>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select class="form-select" id="tahun" name="tahun" required>
                                    @for($i=date('Y'); $i<=date('Y')+1; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-calculator"></i> Hitung
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hasil Prediksi -->
            @if(isset($prediksi))
            <div class="row">
                <div class="col-md-12">
                    <!-- Rekomendasi Metode -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Rekomendasi Metode Prediksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success">
                                <h4 class="alert-heading">
                                    <i class="bi bi-check-circle"></i> Metode Terbaik: {{ $rekomendasiMetode['metode'] }}
                                </h4>
                                <p class="mb-2"><strong>Alasan:</strong> {{ $rekomendasiMetode['alasan'] }}</p>
                                <p class="mb-0"><strong>Prediksi Penjualan:</strong> {{ $rekomendasiMetode['prediksi'] }} unit</p>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            Simple Moving Average (SMA)
                                        </div>
                                        <div class="card-body">
                                            <h4 class="text-center">{{ $prediksi->sma }} unit</h4>
                                            <div class="mt-3">
                                                <p class="mb-1"><strong>Window Size:</strong> {{ $perbandinganMetode['SMA']['window_size'] }} bulan</p>
                                                <p class="mb-1"><strong>MAPE:</strong> {{ $perbandinganMetode['SMA']['mape'] }}</p>
                                                <p class="mb-1"><strong>MSE:</strong> {{ $perbandinganMetode['SMA']['mse'] }}</p>
                                                <p class="mb-1"><strong>MAE:</strong> {{ $perbandinganMetode['SMA']['mae'] }}</p>
                                                <p class="mb-0"><strong>Rumus:</strong> {{ $perbandinganMetode['SMA']['rumus'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-warning text-dark">
                                            Weighted Moving Average (WMA)
                                        </div>
                                        <div class="card-body">
                                            <h4 class="text-center">{{ $prediksi->wma }} unit</h4>
                                            <div class="mt-3">
                                                <p class="mb-1"><strong>Window Size:</strong> {{ $perbandinganMetode['WMA']['window_size'] }} bulan</p>
                                                <p class="mb-1"><strong>Bobot:</strong> {{ $perbandinganMetode['WMA']['weights'] }}</p>
                                                <p class="mb-1"><strong>MAPE:</strong> {{ $perbandinganMetode['WMA']['mape'] }}</p>
                                                <p class="mb-1"><strong>MSE:</strong> {{ $perbandinganMetode['WMA']['mse'] }}</p>
                                                <p class="mb-1"><strong>MAE:</strong> {{ $perbandinganMetode['WMA']['mae'] }}</p>
                                                <p class="mb-0"><strong>Rumus:</strong> {{ $perbandinganMetode['WMA']['rumus'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Perbandingan -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Grafik Perbandingan Metode</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartPerbandingan" style="height: 300px;"></canvas>
                        </div>
                    </div>

                    <!-- Detail Perhitungan -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Detail Perhitungan dan Evaluasi</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2">Bulan</th>
                                            <th rowspan="2">Aktual</th>
                                            <th colspan="2" class="text-center">Prediksi</th>
                                            <th colspan="2" class="text-center">Error</th>
                                        </tr>
                                        <tr>
                                            <th>SMA</th>
                                            <th>WMA</th>
                                            <th>SMA</th>
                                            <th>WMA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detailPerhitungan as $detail)
                                        <tr>
                                            <td>{{ $detail['bulan'] }}</td>
                                            <td class="text-end">{{ number_format($detail['aktual']) }}</td>
                                            <td class="text-end">{{ number_format($detail['sma'], 2) }}</td>
                                            <td class="text-end">{{ number_format($detail['wma'], 2) }}</td>
                                            <td class="text-end {{ $detail['error_sma'] < 0 ? 'text-danger' : '' }}">
                                                {{ number_format($detail['error_sma'], 2) }}
                                            </td>
                                            <td class="text-end {{ $detail['error_wma'] < 0 ? 'text-danger' : '' }}">
                                                {{ number_format($detail['error_wma'], 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4">Rata-rata Error</th>
                                            <th class="text-end">{{ number_format(array_sum(array_column($detailPerhitungan, 'error_sma'))/count($detailPerhitungan), 2) }}</th>
                                            <th class="text-end">{{ number_format(array_sum(array_column($detailPerhitungan, 'error_wma'))/count($detailPerhitungan), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                <h5>Keterangan:</h5>
                                <ul>
                                    <li><strong>MAPE (Mean Absolute Percentage Error):</strong> Mengukur akurasi prediksi dalam persentase. Semakin kecil semakin baik.</li>
                                    <li><strong>MSE (Mean Squared Error):</strong> Mengukur rata-rata kuadrat error. Memberikan penalti lebih besar untuk error yang besar.</li>
                                    <li><strong>MAE (Mean Absolute Error):</strong> Mengukur rata-rata absolute error. Lebih mudah diinterpretasi daripada MSE.</li>
                                    <li><strong>Error positif</strong> berarti prediksi lebih rendah dari aktual (under-prediction).</li>
                                    <li><strong>Error negatif</strong> berarti prediksi lebih tinggi dari aktual (over-prediction).</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Historis dan Prediksi -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Grafik Historis dan Prediksi</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartHistoris" style="height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($prediksi))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Perbandingan Metode
    const ctx1 = document.getElementById('chartPerbandingan').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['SMA', 'WMA'],
            datasets: [{
                label: 'Hasil Prediksi (unit)',
                data: [{{ $prediksi->sma }}, {{ $prediksi->wma }}],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Prediksi (unit)'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Perbandingan Hasil Prediksi SMA vs WMA'
                }
            }
        }
    });

    // Chart Historis dan Prediksi
    const ctx2 = document.getElementById('chartHistoris').getContext('2d');
    const labels = {!! json_encode(array_column($detailPerhitungan, 'bulan')) !!};
    const aktual = {!! json_encode(array_column($detailPerhitungan, 'aktual')) !!};
    const sma = {!! json_encode(array_column($detailPerhitungan, 'sma')) !!};
    const wma = {!! json_encode(array_column($detailPerhitungan, 'wma')) !!};

    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Aktual',
                    data: aktual,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: false
                },
                {
                    label: 'Prediksi SMA',
                    data: sma,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.1,
                    fill: false
                },
                {
                    label: 'Prediksi WMA',
                    data: wma,
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.1,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Penjualan (unit)'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Perbandingan Data Aktual dengan Prediksi'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toFixed(2) + ' unit';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection