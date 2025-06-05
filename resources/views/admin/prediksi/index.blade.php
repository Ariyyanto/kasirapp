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
                <div class="col-md-6">
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
                            <div class="col-md-4">
                                <label for="bulan" class="form-label">Bulan</label>
                                <select class="form-select" id="bulan" name="bulan" required>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select class="form-select" id="tahun" name="tahun" required>
                                    @for($i=date('Y'); $i<=date('Y')+1; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calculator"></i> Hitung Prediksi
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
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Hasil Prediksi untuk {{ $produkTerpilih->nama_produk }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            Simple Moving Average (SMA)
                                        </div>
                                        <div class="card-body">
                                            <h4 class="text-center">{{ $prediksi->jumlah_prediksi }} unit</h4>
                                            <p class="mb-1"><strong>MAPE:</strong> {{ number_format($prediksi->mape, 2) }}%</p>
                                            <p class="mb-1"><strong>MSE:</strong> {{ number_format($prediksi->mse, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            Weighted Moving Average (WMA)
                                        </div>
                                        <div class="card-body">
                                            <h4 class="text-center">{{ $prediksi->jumlah_prediksi_wma ?? 'N/A' }} unit</h4>
                                            <p class="mb-1"><strong>MAPE:</strong> {{ isset($prediksi->mape_wma) ? number_format($prediksi->mape_wma, 2).'%' : 'N/A' }}</p>
                                            <p class="mb-1"><strong>MSE:</strong> {{ isset($prediksi->mse_wma) ? number_format($prediksi->mse_wma, 2) : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Perbandingan -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Grafik Perbandingan</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartPrediksi" style="height: 300px;"></canvas>
                        </div>
                    </div>

                    <!-- Detail Perhitungan -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Detail Perhitungan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Aktual</th>
                                            <th>SMA</th>
                                            <th>WMA</th>
                                            <th>Error SMA</th>
                                            <th>Error WMA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detailPerhitungan as $detail)
                                        <tr>
                                            <td>{{ $detail['bulan'] }}</td>
                                            <td>{{ $detail['aktual'] ?? '-' }}</td>
                                            <td>{{ $detail['sma'] ?? '-' }}</td>
                                            <td>{{ $detail['wma'] ?? '-' }}</td>
                                            <td>{{ $detail['error_sma'] ?? '-' }}</td>
                                            <td>{{ $detail['error_wma'] ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
        const ctx = document.getElementById('chartPrediksi').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['SMA', 'WMA'],
                datasets: [{
                    label: 'Hasil Prediksi',
                    data: [
                        {{ $prediksi->jumlah_prediksi }}, 
                        {{ $prediksi->jumlah_prediksi_wma ?? $prediksi->jumlah_prediksi }}
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)'
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
                            text: 'Jumlah Prediksi'
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection