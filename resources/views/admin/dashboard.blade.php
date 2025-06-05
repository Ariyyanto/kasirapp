@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --dark-gradient: linear-gradient(135deg, #323232 0%, #3F3F3F 100%);
    }
    
    body {
        background-color: #f8f9fc;
    }
    
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.5rem 1.5rem 0 rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        border-bottom: none;
        background-color: white;
        padding: 1.25rem 1.5rem;
    }
    
    .stat-card {
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
    }
    
    .stat-card-primary::before {
        background: var(--primary-gradient);
    }
    
    .stat-card-success::before {
        background: var(--success-gradient);
    }
    
    .stat-card-info::before {
        background: var(--info-gradient);
    }
    
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 12px;
    }
    
    .icon-shape-primary {
        background: var(--primary-gradient);
        color: white;
    }
    
    .icon-shape-success {
        background: var(--success-gradient);
        color: white;
    }
    
    .icon-shape-info {
        background: var(--info-gradient);
        color: white;
    }
    
    .date-display {
        font-weight: 600;
        background: white;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        color: #667eea;
        border: 1px solid rgba(102, 126, 234, 0.2);
    }
    
    .chart-container {
        position: relative;
        height: 350px;
        padding: 0 15px;
    }
    
    .activity-item {
        border-left: 3px solid;
        padding-left: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    
    .activity-item:hover {
        transform: translateX(5px);
    }
    
    .activity-primary {
        border-left-color: #667eea;
    }
    
    .activity-success {
        border-left-color: #43e97b;
    }
    
    .activity-warning {
        border-left-color: #ffc107;
    }
    
    .activity-danger {
        border-left-color: #f5576c;
    }
    
    .dropdown-toggle::after {
        display: none;
    }
    
    .filter-btn {
        border: 1px solid rgba(102, 126, 234, 0.3);
        border-radius: 8px;
        padding: 0.375rem 0.75rem;
    }
    
    .filter-btn:hover {
        background: rgba(102, 126, 234, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-gradient" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Dashboard Overview</h2>
        <div class="date-display">
            <i class="fas fa-calendar-alt me-2"></i>
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card stat-card-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-muted mb-2">Total Produk</h5>
                            <h2 class="mb-0">{{ $totalProduk }}</h2>
                            <small class="text-muted">+5.2% dari bulan lalu</small>
                        </div>
                        <div class="icon-shape icon-shape-primary">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card stat-card-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-muted mb-2">Total Penjualan</h5>
                            <h2 class="mb-0">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h2>
                            <small class="text-muted">+12.7% dari bulan lalu</small>
                        </div>
                        <div class="icon-shape icon-shape-success">
                            <i class="fas fa-cash-register fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card stat-card-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-muted mb-2">Produk Terlaris</h5>
                            <h2 class="mb-0">
                                @if($produkTerlaris)
                                    {{ $produkTerlaris->produk->nama_produk }}
                                    <small class="d-block text-muted">{{ number_format($produkTerlaris->total, 0, ',', '.') }} terjual</small>
                                @else
                                    -
                                @endif
                            </h2>
                        </div>
                        <div class="icon-shape icon-shape-info">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sales Chart -->
    <div class="card mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Grafik Penjualan</h5>
            <div class="dropdown">
                <button class="btn btn-sm filter-btn dropdown-toggle" type="button" id="chartDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i> 
                    @if($filter == 'today') Hari Ini
                    @elseif($filter == '7days') 7 Hari Terakhir
                    @elseif($filter == '30days') 30 Hari Terakhir
                    @elseif($filter == 'month') Bulan Ini
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="?filter=today">Hari Ini</a></li>
                    <li><a class="dropdown-item" href="?filter=7days">7 Hari Terakhir</a></li>
                    <li><a class="dropdown-item" href="?filter=30days">30 Hari Terakhir</a></li>
                    <li><a class="dropdown-item" href="?filter=month">Bulan Ini</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Aktivitas Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($activities as $activity)
                        <div class="list-group-item border-0 px-0 py-3">
                            <div class="activity-item activity-{{ $activity['color'] }}">
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape bg-light-{{ $activity['color'] }} text-{{ $activity['color'] }} rounded-circle me-3">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $activity['message'] }}</h6>
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item border-0 px-0">
                            <p class="text-muted mb-0">Tidak ada aktivitas terakhir</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-gradient-colors"></script>
<script>
    $(document).ready(function() {
        // Gradient function
        function createGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: @json($data),
                    backgroundColor: createGradient(ctx, 'rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.4)'),
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 0,
                    borderRadius: 12,
                    borderSkipped: false,
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            },
                            padding: 10
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#000',
                        bodyColor: '#000',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 15,
                        boxShadow: '0 0.5rem 1rem rgba(0, 0, 0, 0.15)',
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection