@extends('layouts.main', ['title' => 'Home'])

@section('title-content')
<i class="fas fa-home mr-2"></i> 
<span class="font-semibold text-gray-800">Dashboard</span>
@endsection

@push('styles')
<style>
    .dashboard-container {
        background: #f8fafc;
        min-height: 100vh;
        padding: 1.5rem 0;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.75rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e0;
    }
    
    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        transition: all 0.3s ease;
    }
    
    .stat-card.danger::after { background: #ef4444; }
    .stat-card.warning::after { background: #f59e0b; }
    .stat-card.success::after { background: #10b981; }
    .stat-card.primary::after { background: #3b82f6; }
    .stat-card.info::after { background: #06b6d4; }
    
    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }
    
    .stat-icon.danger { background: #ef4444; }
    .stat-icon.warning { background: #f59e0b; }
    .stat-icon.success { background: #10b981; }
    .stat-icon.primary { background: #3b82f6; }
    .stat-icon.info { background: #06b6d4; }
    
    .stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .stat-title {
        color: #6b7280;
        font-weight: 500;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .progress-container {
        margin-top: 1rem;
    }
    
    .progress-bar-custom {
        width: 100%;
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }
    
    .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    
    .progress-fill.danger {
        background-color: #ef4444;
    }
    
    .progress-fill.warning {
        background-color: #f59e0b;
    }
    
    .progress-fill.success {
        background-color: #10b981;
    }
    
    .progress-info {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 500;
        text-align: right;
    }
    
    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .chart-header .left-section {
        display: flex;
        align-items: center;
    }
    
    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }
    
    .chart-icon {
        width: 36px;
        height: 36px;
        background: #f1f5f9;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: #64748b;
    }
    
    .chart-controls {
        display: flex;
        align-items: center;
    }
    
    .chart-controls .btn-group .btn {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }
    
    .chart-controls .btn-group .btn:first-child {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .chart-controls .btn-group .btn:last-child {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: 0;
    }

    @media (max-width: 768px) {
        .chart-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .chart-controls {
            width: 100%;
            justify-content: center;
        }
        
        .chart-controls .btn-group {
            width: 100%;
        }
        
        .chart-controls .btn-group .btn {
            flex: 1;
            text-align: center;
        }
    }
    
    .admin-section {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .admin-badge {
        display: inline-flex;
        align-items: center;
        background: #1f2937;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }
    
    .admin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .user-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .animate-fade-in:nth-child(1) { animation-delay: 0.1s; }
    .animate-fade-in:nth-child(2) { animation-delay: 0.2s; }
    .animate-fade-in:nth-child(3) { animation-delay: 0.3s; }
    .animate-fade-in:nth-child(4) { animation-delay: 0.4s; }
    .animate-fade-in:nth-child(5) { animation-delay: 0.5s; }
    
    .text-decoration-none:hover {
        text-decoration: none !important;
    }
    
    @media (max-width: 768px) {
        .stats-grid,
        .admin-grid,
        .user-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-card {
            padding: 1.5rem;
        }
        
        .chart-card {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="container-fluid">
        @can('admin')
            <div class="admin-section animate-fade-in">
                <div class="admin-badge">
                    <i class="fas fa-shield-alt mr-2"></i>Admin Panel
                </div>
                <div class="admin-grid">
                    <a href="{{ route('user.index') }}" class="text-decoration-none animate-fade-in">
                        <div class="stat-card danger">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-number">{{ $user->jumlah }}</div>
                                    <div class="stat-title">Total Users</div>
                                </div>
                                <div class="stat-icon danger">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('kategori.index') }}" class="text-decoration-none animate-fade-in">
                        <div class="stat-card warning">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-number">{{ $kategori->jumlah }}</div>
                                    <div class="stat-title">Kategori</div>
                                </div>
                                <div class="stat-icon warning">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('diskon.index') }}" class="text-decoration-none animate-fade-in">
                        <div class="stat-card success">
                            <div class="stat-header">
                                <div>
                                    <div class="stat-number">{{ $diskon->jumlah }}</div>
                                    <div class="stat-title">Diskon Aktif</div>
                                </div>
                                <div class="stat-icon success">
                                    <i class="fas fa-tags"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan
        
        <div class="user-grid">
            <a href="{{ route('pelanggan.index') }}" class="text-decoration-none animate-fade-in">
                <div class="stat-card primary">
                    <div class="stat-header">
                        <div>
                            <div class="stat-number">{{ $pelanggan->jumlah }}</div>
                            <div class="stat-title">Total Pelanggan</div>
                        </div>
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{ route('produk.index') }}" class="text-decoration-none animate-fade-in">
                <div class="stat-card info">
                    <div class="stat-header">
                        <div>
                            <div class="stat-number">{{ $produk->jumlah }}</div>
                            <div class="stat-title">Produk Tersedia</div>
                        </div>
                        <div class="stat-icon info">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="chart-card animate-fade-in mt-4">
            <div class="chart-header">
                <div class="left-section">
                    <div class="chart-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="chart-title">Analytics Overview</h3>
                </div>
                <div class="chart-controls">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('dashboard', ['mode' => 'daily']) }}" 
                           class="btn {{ ($current_mode ?? 'daily') === 'daily' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-calendar-day"></i> Harian
                        </a>
                        <a href="{{ route('dashboard', ['mode' => 'monthly']) }}" 
                           class="btn {{ ($current_mode ?? 'daily') === 'monthly' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-calendar-alt"></i> Bulanan
                        </a>
                    </div>
                </div>
            </div>
            <div style="position: relative; height: 400px;">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="chart-card animate-fade-in mt-4">
                    <div class="chart-header">
                        <div class="chart-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h3 class="chart-title">Pembelian Stok per Bulan</h3>
                    </div>
                    <div style="position: relative; height: 400px;">
                        <canvas id="pengeluaranChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card animate-fade-in mt-4">
                    <div class="chart-header">
                        <div class="chart-icon">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <h3 class="chart-title">Produk Terjual Bulan Ini</h3>
                    </div>
                    <div style="position: relative; height: 400px;">
                        <canvas id="produkTerjualChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart Keuntungan/Kerugian per Bulan -->
    <div class="col-xl-12 col-md-12 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title text-primary fw-bold mb-3">
                        <i class="fas fa-chart-line me-2"></i>Keuntungan/Kerugian per Bulan
                    </h6>
                </div>
                <div style="position: relative; height: 400px;">
                    <canvas id="keuntunganKerugianChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Target Cards -->
        <div class="user-grid mt-4">
            <div class="stat-card {{ $target_harian['percentage'] < 25 ? 'danger' : ($target_harian['percentage'] < 75 ? 'warning' : 'success') }}">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">
                            @if($target_harian['percentage'] >= 100)
                                <span style="font-size: 1.5rem; color: #10b981;">✓ Terpenuhi</span>
                            @else
                                Rp {{ number_format($target_harian['target'], 0, ',', '.') }}
                            @endif
                        </div>
                        <div class="stat-title">Target Harian</div>
                    </div>
                    <div class="stat-icon {{ $target_harian['percentage'] < 25 ? 'danger' : ($target_harian['percentage'] < 75 ? 'warning' : 'success') }}">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-custom">
                        <div class="progress-fill {{ $target_harian['percentage'] < 25 ? 'danger' : ($target_harian['percentage'] < 75 ? 'warning' : 'success') }}" 
                             style="width: {{ $target_harian['percentage'] }}%"></div>
                    </div>
                    <div class="progress-info">
                        {{ number_format($target_harian['current'], 0, ',', '.') }}/{{ number_format($target_harian['target'], 0, ',', '.') }} ({{ $target_harian['percentage'] }}%)
                    </div>
                </div>
            </div>
            
            <div class="stat-card {{ $target_bulanan['percentage'] < 25 ? 'danger' : ($target_bulanan['percentage'] < 75 ? 'warning' : 'success') }}">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">
                            @if($target_bulanan['percentage'] >= 100)
                                <span style="font-size: 1.5rem; color: #10b981;">✓ Terpenuhi</span>
                            @else
                                Rp {{ number_format($target_bulanan['target'], 0, ',', '.') }}
                            @endif
                        </div>
                        <div class="stat-title">Target Bulanan</div>
                    </div>
                    <div class="stat-icon {{ $target_bulanan['percentage'] < 25 ? 'danger' : ($target_bulanan['percentage'] < 75 ? 'warning' : 'success') }}">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-custom">
                        <div class="progress-fill {{ $target_bulanan['percentage'] < 25 ? 'danger' : ($target_bulanan['percentage'] < 75 ? 'warning' : 'success') }}" 
                             style="width: {{ $target_bulanan['percentage'] }}%"></div>
                    </div>
                    <div class="progress-info">
                        {{ number_format($target_bulanan['current'], 0, ',', '.') }}/{{ number_format($target_bulanan['target'], 0, ',', '.') }} ({{ $target_bulanan['percentage'] }}%)
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('myChart').getContext('2d');
        var produkTerjualCtx = document.getElementById('produkTerjualChart').getContext('2d');
        var pengeluaranCtx = document.getElementById('pengeluaranChart').getContext('2d');
        var keuntunganKerugianCtx = document.getElementById('keuntunganKerugianChart').getContext('2d');

        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! $cart['labels'] !!},
                datasets: [{
                    label: "{{ $cart['label'] }}",
                    data: {!! $cart['data'] !!},
                    borderWidth: 3,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#1d4ed8',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6b7280',
                            padding: 10,
                            callback: function(value) {
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6b7280',
                            padding: 10
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutCubic'
                }
            }
        });

        var produkTerjualChart = new Chart(produkTerjualCtx, {
            type: 'doughnut',
            data: {
                labels: {!! $produk_terjual['labels'] !!},
                datasets: [{
                    label: "{{ $produk_terjual['label'] }}",
                    data: {!! $produk_terjual['data'] !!},
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#6366f1', '#f472b6', '#fbbf24', '#a3e635', '#f87171', '#34d399', '#818cf8'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151'
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutCubic'
                }
            }
        });

        var pengeluaranChart = new Chart(pengeluaranCtx, {
            type: 'bar',
            data: {
                labels: {!! $pengeluaran_stok['labels'] !!},
                datasets: [{
                    label: "{{ $pengeluaran_stok['label'] }}",
                    data: {!! $pengeluaran_stok['data'] !!},
                    backgroundColor: '#ef4444',
                    borderColor: '#c53030',
                    borderWidth: 2,
                    hoverBackgroundColor: '#f56565',
                    hoverBorderColor: '#c53030',
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6b7280',
                            padding: 10,
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6b7280',
                            padding: 10
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutCubic'
                }
            }
        });

        // Chart Keuntungan/Kerugian dengan style seperti candlestick
        var keuntunganKerugianChart = new Chart(keuntunganKerugianCtx, {
            type: 'bar',
            data: {
                labels: {!! $keuntungan_kerugian['labels'] !!},
                datasets: [{
                    label: "{{ $keuntungan_kerugian['label'] }}",
                    data: {!! $keuntungan_kerugian['data'] !!},
                    backgroundColor: {!! $keuntungan_kerugian['colors'] !!},
                    borderColor: {!! $keuntungan_kerugian['colors'] !!},
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var value = context.parsed.y;
                                var status = value >= 0 ? 'Keuntungan' : 'Kerugian';
                                return status + ': Rp ' + Math.abs(value).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6b7280',
                            padding: 10,
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6b7280',
                            padding: 10
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutCubic'
                }
            }
        });

    });
</script>
@endpush