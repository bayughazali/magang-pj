@extends('layouts.app')

@section('content')
<style>

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    
    .bg-gradient-pink {
        background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
    }

    .chart-card {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .chart-title {
        font-size: 18px;
        font-weight: bold;
        color: #2c3e50;
        padding: 20px 20px 10px;
    }

    .chart-subtitle {
        font-size: 13px;
        color: #7f8c8d;
        padding: 0 20px 10px;
    }

    /* Responsif untuk mobile */
    @media (max-width: 768px) {
        .chart-container {
            height: 300px !important;
        }
        
        .col-md-3 {
            margin-bottom: 15px;
        }
    }

    .header-bg {
        background: linear-gradient(90deg, #4c6ef5, #6a92ff);
        padding: 30px 0 120px 0;
        border-radius: 0 0 18px 18px;
        overflow: visible !important;
    }
    .card-stats {
        border-radius: 14px;
        transition: .2s;
        position: relative;
        z-index: auto;
    }
    .card-stats:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }
    .card-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 0 0 14px 14px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        z-index: 9999 !important;
        display: none;
        margin-top: -14px;
        padding-top: 14px;
    }
    .card-dropdown.show {
        display: block !important;
    }
    .card-dropdown a {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    .card-dropdown a:last-child { border-bottom: none; }
    .card-dropdown a:hover {
        background: #f8f9fa;
        color: #4c6ef5;
    }
    .card-clickable {
        cursor: pointer;
        user-select: none;
    }
    .chart-card {
        border-radius: 14px;
        padding: 20px;
        min-height: 400px;
    }
    .chart-container {
        position: relative;
        height: 280px;
        margin-top: 10px;
    }
    .table-card { border-radius: 14px; }
    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
    .chart-subtitle {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: -10px;
        margin-bottom: 15px;
    }
    /* ✅ Style tambahan untuk sales cards */
    .hover-card {
        transition: all 0.3s ease;
        border-radius: 10px;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    }
    .avatar-circle {
        flex-shrink: 0;
    }
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-bar {
        transition: width 1s ease;
    }

    /* ✅ Timeline Styling */
    .timeline {
        position: relative;
        max-height: 600px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .timeline::-webkit-scrollbar {
        width: 6px;
    }

    .timeline::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .timeline::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .timeline::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .timeline-item {
        position: relative;
        animation: slideInLeft 0.5s ease-out;
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .timeline-badge {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }

    /* ✅ Gradient backgrounds for metric cards */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .bg-gradient-pink {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
</style>

<div class="header-bg">
    <div class="container-fluid" style="margin-top: 30px;">
        <h2 class="text-white mb-3">Dashboard</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home text-white"></i></a></li>
                <li class="breadcrumb-item text-white">Dashboards</li>
                <li class="breadcrumb-item active text-white">Default</li>
            </ol>
        </nav>

        <div class="row mt-4 {{ auth()->user()->role !== 'admin' ? 'justify-content-center' : '' }}">
    {{-- 🔴 PERUBAHAN: SALES REPORT --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stats border-0 shadow-sm bg-white card-clickable"
             onclick="toggleDropdown('salesDropdown', event)">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Sales Report</h6>
                        <h2 class="font-weight-bold mb-0 text-dark" id="countSales">
                            {{ number_format($totalReportBulanIni ?? 0) }}
                        </h2>
                        <small class="text-muted">
                            {{-- 🔴 PERUBAHAN: Text dinamis berdasarkan role --}}
                            @if(auth()->user()->role === 'admin')
                                Total laporan bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
                            @else
                                Laporan Anda bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
                            @endif
                        </small>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="{{ $persenSales >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fa fa-arrow-{{ $persenSales >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($persenSales), 2) }}%
                            </span> dibanding bulan lalu
                        </p>
                    </div>
                    <div><i class="ni ni-chart-bar-32 display-4 text-primary"></i></div>
                </div>
            </div>
            <div class="card-dropdown" id="salesDropdown">
                <a href="{{ route('reports.activity') }}"><i class="ni ni-bullet-list-67"></i> Report Activity</a>
                <a href="{{ route('reports.competitor') }}"><i class="ni ni-chart-pie-35"></i> Report Competitor</a>
            </div>
        </div>
    </div>

    {{-- 🔴 PERUBAHAN: OPERATIONAL REPORT --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stats border-0 shadow-sm bg-white card-clickable"
             onclick="toggleDropdown('operationalDropdown', event)">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Operational Report</h6>
                        <h2 class="font-weight-bold mb-0 text-dark" id="countPelanggan">
                            {{ number_format($totalPelangganBulanIni ?? 0) }}
                        </h2>
                        <small class="text-muted">
                            {{-- 🔴 PERUBAHAN: Text dinamis berdasarkan role --}}
                            @if(auth()->user()->role === 'admin')
                                Data pelanggan bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
                            @else
                                Data pelanggan Anda bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
                            @endif
                        </small>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="{{ $persenPelanggan >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fa fa-arrow-{{ $persenPelanggan >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($persenPelanggan), 2) }}%
                            </span> dibanding bulan lalu
                        </p>
                    </div>
                    <div><i class="ni ni-laptop display-4 text-success"></i></div>
                </div>
            </div>
            <div class="card-dropdown" id="operationalDropdown">
                <a href="{{ route('report.operational.index') }}"><i class="ni ni-archive-2"></i> Input Data Pelanggan</a>
                <a href="{{ route('report.customer.search') }}"><i class="ni ni-zoom-split-in"></i> Cari Pelanggan & Kode FAT</a>
            </div>
        </div>
    </div>

            {{-- USER MANAGEMENT --}}
            @if(auth()->user()->role === 'admin')
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0 shadow-sm bg-white card-clickable"
                     onclick="toggleDropdown('usersDropdown', event)">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">User Management</h6>
                                <h2 class="font-weight-bold mb-0 text-dark" id="countUsers">
                                    {{ number_format($totalUsers ?? 0) }}
                                </h2>
                                <small class="text-muted">Total user terdaftar</small>
                                <p class="mt-3 mb-0 text-sm">
                                    <span class="{{ $persenUsers >= 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fa fa-arrow-{{ $persenUsers >= 0 ? 'up' : 'down' }}"></i>
                                        {{ number_format(abs($persenUsers), 2) }}%
                                    </span> user baru bulan ini
                                </p>
                            </div>
                            <div><i class="ni ni-single-02 display-4 text-info"></i></div>
                        </div>
                    </div>
                    <div class="card-dropdown" id="usersDropdown">
                        <a href="{{ route('users.index') }}"><i class="ni ni-single-02"></i> User</a>
                        <a href="{{ route('admins.index') }}"><i class="ni ni-badge"></i> Admin</a>
                    </div>
                </div>
            </div>
            @endif

            {{-- EXPORT DATA --}}
            @if(auth()->user()->role === 'admin')
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0 shadow-sm bg-white card-clickable"
                     onclick="toggleDropdown('exportDropdown', event)">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Export Data</h6>
                                <h2 class="font-weight-bold mb-0 text-dark" id="countExport"><i class="ni ni-cloud-download-95"></i></h2>
                                <small class="text-muted">Download laporan</small>
                                <p class="mt-3 mb-0 text-sm text-muted">Klik untuk pilihan export</p>
                            </div>
                            <div><i class="ni ni-archive-2 display-4 text-purple"></i></div>
                        </div>
                    </div>
                    <div class="card-dropdown" id="exportDropdown">
                        <a href="{{ route('export.activity') }}"><i class="ni ni-single-copy-04"></i> Report Activity</a>
                        <a href="{{ route('export.competitor') }}"><i class="ni ni-single-copy-04"></i> Report Competitor</a>
                        <a href="{{ route('export.operational') }}"><i class="ni ni-single-copy-04"></i> Report Operational</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ✅ AKTIVITAS SALES REPORT PER USER --}}
<div class="col-xl-12 mb-4">
    <div class="card chart-card">
        {{-- 🔴 PERUBAHAN: Judul dinamis berdasarkan role --}}
        <div class="chart-title">
            <i class="fas fa-user-tie text-primary me-2"></i>
            @if(auth()->user()->role === 'admin')
                Aktivitas Sales Report Per User - {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
            @else
                Aktivitas Sales Report Saya - {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
            @endif
        </div>
        {{-- 🔴 PERUBAHAN: Subtitle dinamis berdasarkan role --}}
        <div class="chart-subtitle">
            @if(auth()->user()->role === 'admin')
                Klik pada card untuk melihat detail aktivitas tiap sales
            @else
                Klik pada card untuk melihat detail aktivitas sales Anda
            @endif
        </div>

        @if($salesActivities->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                @if(auth()->user()->role === 'admin')
                    <p class="text-muted">Belum ada aktivitas sales bulan ini</p>
                @else
                    <p class="text-muted">Anda belum memiliki aktivitas sales bulan ini</p>
                @endif
            </div>
        @else
            {{-- 🔴 PERUBAHAN: Layout responsive - jika admin tampilkan grid, jika user centered --}}
            <div class="row g-3 mt-2 {{ auth()->user()->role !== 'admin' ? 'justify-content-center' : '' }}">
                @foreach($salesActivities as $sales)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border shadow-sm hover-card"
                             style="cursor: pointer; transition: all 0.3s;"
                             onclick="showSalesDetail('{{ $sales['name'] }}')">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">

                                    {{-- ✅ Tampilkan foto user jika ada, jika tidak pakai avatar initial --}}
                                    @if($sales['photo'])
    <img src="{{ $sales['photo'] }}"
         alt="{{ $sales['name'] }}"
         class="rounded-circle me-3"
         style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #dee2e6;"
         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
    <div class="avatar-circle bg-{{ $sales['color'] }} text-white rounded-circle align-items-center justify-content-center me-3"
         style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold; display: none;">
        {{ $sales['initial'] }}
    </div>
@else
    <div class="avatar-circle bg-{{ $sales['color'] }} text-white rounded-circle d-flex align-items-center justify-content-center me-3"
         style="width: 50px; height: 50px; font-size: 1.5rem; font-weight: bold;">
        {{ $sales['initial'] }}
    </div>
@endif

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $sales['name'] }}</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.875rem;">
                                            @if($sales['total'] > 0)
                                                {{ $sales['total'] }} aktivitas bulan ini
                                            @else
                                                <span class="text-secondary">Belum ada aktivitas</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $sales['color'] }}"
                                         role="progressbar"
                                         style="width: {{ $sales['progress'] }}%"
                                         aria-valuenow="{{ $sales['progress'] }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

        {{-- ✅ GRAFIK KORELASI: Aktivitas Sales vs Hasil Penjualan --}}
        {{-- ✅ GRAFIK KORELASI: Aktivitas Sales vs Hasil Penjualan --}}
<div class="col-xl-12 mb-4">
    <div class="card chart-card">
        <div class="chart-title">
            <i class="fas fa-chart-line text-success me-2"></i>
            Korelasi Aktivitas Sales vs Hasil Penjualan - {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
        </div>
        <div class="chart-subtitle">
            Grafik menunjukkan <strong>kinerja tiap sales</strong> berdasarkan jumlah aktivitas dan total pendapatan yang dihasilkan dari laporan penjualan.
        </div>

        {{-- Filter User (opsional) --}}
        @if(!empty($salesCorrelationData))
        <div class="p-3">
            <label for="filterUser" class="form-label mb-2 fw-semibold">Tampilkan Kinerja:</label>
            <select id="filterUser" class="form-select w-auto">
                <option value="">Semua Sales</option>
                @foreach($salesCorrelationData as $sales)
                    <option value="{{ $sales['name'] }}">{{ $sales['name'] }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if(empty($salesCorrelationData) || count($salesCorrelationData) == 0)
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada data korelasi bulan ini</p>
            </div>
        @else
            <div class="chart-container" style="height: 350px;">
                <canvas id="chart-correlation"></canvas>
            </div>

            {{-- Info Card --}}
            @php
                $topPerformer = collect($salesCorrelationData)->sortByDesc('sales')->first();
                $totalSales = collect($salesCorrelationData)->sum('sales');
                $totalRevenue = collect($salesCorrelationData)->sum('revenue');
                $activeUsers = collect($salesCorrelationData)->where('sales', '>', 0)->count();
            @endphp

            <div class="row mt-4 g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-gradient-primary">
                        <div class="card-body text-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-trophy fa-2x me-3"></i>
                                <div>
                                    <h6 class="text-uppercase text-white-50 mb-1" style="font-size: 0.75rem;">Top Performer</h6>
                                    <h4 class="mb-0 fw-bold">{{ $topPerformer['name'] ?? '-' }}</h4>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3"
                                 style="border-top: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 0.85rem;">Penjualan:</span>
                                <strong style="font-size: 1.1rem;">{{ $topPerformer['sales'] ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-gradient-info">
                        <div class="card-body text-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-chart-line fa-2x me-3"></i>
                                <div>
                                    <h6 class="text-uppercase text-white-50 mb-1" style="font-size: 0.75rem;">Total Penjualan</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalSales }}</h4>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3"
                                 style="border-top: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 0.85rem;">Bulan ini</span>
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-gradient-success">
                        <div class="card-body text-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-money-bill-wave fa-2x me-3"></i>
                                <div>
                                    <h6 class="text-uppercase text-white-50 mb-1" style="font-size: 0.75rem;">Total Revenue</h6>
                                    <h4 class="mb-0 fw-bold">Rp {{ number_format($totalRevenue / 1000000, 1) }}Jt</h4>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3"
                                 style="border-top: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 0.85rem;">Dari {{ $totalSales }} transaksi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-gradient-pink">
                        <div class="card-body text-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-users fa-2x me-3"></i>
                                <div>
                                    <h6 class="text-uppercase text-white-50 mb-1" style="font-size: 0.75rem;">Sales Aktif</h6>
                                    <h4 class="mb-0 fw-bold">{{ $activeUsers }}</h4>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3"
                                 style="border-top: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 0.85rem;">Melakukan penjualan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ✅ MODAL DETAIL AKTIVITAS SALES --}}
<div class="modal fade" id="salesDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, #4c6ef5, #6a92ff);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Detail Aktivitas <span id="salesNameTitle"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="salesDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ MODAL DETAIL COMPETITOR --}}
<div class="modal fade" id="competitorDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, #ee0979, #ff6a00);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-users me-2"></i>
                    Detail Competitor <span id="competitorSalesName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="competitorDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bulanLabels = {!! json_encode($bulanLabels ?? []) !!};
    const pelangganTren = {!! json_encode($pelangganTren ?? []) !!};
    const clusterLabels = {!! json_encode($clusterLabels ?? []) !!};
    const clusterValues = {!! json_encode($clusterValues ?? []) !!};

    // BAR CHART (batang tebal)
    if (clusterLabels.length > 0 && clusterValues.length > 0) {
        new Chart(document.getElementById('chart-bar'), {
            type: 'bar',
            data: {
                labels: clusterLabels,
                datasets: [{
                    label: 'Jumlah Pelanggan',
                    data: clusterValues,
                    backgroundColor: ['#4c6ef5','#6a92ff','#28a745','#ffc107','#dc3545','#17a2b8'],
                    borderRadius: 10,
                    barThickness: 50,
                    maxBarThickness: 60,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { precision: 0 } },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6c757d', font: { size: 12 } },
                        barPercentage: 0.5,
                        categoryPercentage: 0.6
                    }
                }
            }
        });
    }

    // LINE CHART
    if (bulanLabels.length > 0 && pelangganTren.length > 0) {
        new Chart(document.getElementById('chart-line'), {
            type: 'line',
            data: {
                labels: bulanLabels,
                datasets: [{
                    label: 'Total Pelanggan',
                    data: pelangganTren,
                    borderColor: '#4c6ef5',
                    backgroundColor: 'rgba(76, 110, 245, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#4c6ef5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ✅ CORRELATION CHART: Perbandingan Kinerja Penjualan Per Sales
    const correlationData = {!! json_encode($salesCorrelationData ?? []) !!};

    if (correlationData && correlationData.length > 0) {
        const salesNames = correlationData.map(item => item.name);
        const salesData = correlationData.map(item => item.sales);
        const revenueData = correlationData.map(item => item.revenue);

        new Chart(document.getElementById('chart-correlation'), {
            type: 'bar',
            data: {
                labels: salesNames,
                datasets: [
                    {
                        label: 'Jumlah Penjualan',
                        data: salesData,
                        backgroundColor: 'rgba(76, 110, 245, 0.85)',
                        borderColor: '#4c6ef5',
                        borderWidth: 2,
                        borderRadius: 8,
                        barThickness: 40,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Total Revenue (Juta Rp)',
                        data: revenueData.map(val => val / 1000000),
                        backgroundColor: 'rgba(40, 167, 69, 0.85)',
                        borderColor: '#28a745',
                        borderWidth: 2,
                        borderRadius: 8,
                        barThickness: 40,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { 
                                size: 13, 
                                weight: 'bold',
                                family: "'Segoe UI', 'Arial', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 15,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.yAxisID === 'y1') {
                                    label += 'Rp ' + (context.parsed.y * 1000000).toLocaleString('id-ID');
                                } else {
                                    label += context.parsed.y + ' penjualan';
                                }
                                return label;
                            }
                        }
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        grid: { 
                            color: 'rgba(0,0,0,0.06)',
                            lineWidth: 1
                        },
                        ticks: {
                            precision: 0,
                            font: { size: 12 },
                            color: '#4c6ef5',
                            callback: function(value) {
                                return value + ' sales';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Penjualan',
                            font: { size: 13, weight: 'bold' },
                            color: '#4c6ef5',
                            padding: { top: 10, bottom: 5 }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: { 
                            drawOnChartArea: false
                        },
                        ticks: {
                            font: { size: 12 },
                            color: '#28a745',
                            callback: function(value) {
                                return 'Rp ' + value.toFixed(1) + 'Jt';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Total Revenue',
                            font: { size: 13, weight: 'bold' },
                            color: '#28a745',
                            padding: { top: 10, bottom: 5 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { 
                                size: 12,
                                weight: '600'
                            },
                            color: '#2c3e50',
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }
});

// ✅ SHOW SALES DETAIL MODAL
function showSalesDetail(salesName) {
    const modal = new bootstrap.Modal(document.getElementById('salesDetailModal'));
    document.getElementById('salesNameTitle').textContent = salesName;

    // Show loading
    document.getElementById('salesDetailContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Memuat data aktivitas...</p>
        </div>
    `;

    modal.show();

    // Fetch data
    fetch(`/dashboard/sales-details?sales=${encodeURIComponent(salesName)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = '';

            // ✅ Tampilkan info user jika ada
            // if (data.user) {
            //     html += `
            //         <div class="card border-0 bg-light mb-3">
            //             <div class="card-body">
            //                 <div class="d-flex align-items-center">
            //                     ${data.user.photo ? `
            //                         <img src="${data.user.photo}"
            //                              alt="${data.user.name}"
            //                              class="rounded-circle me-3"
            //                              style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            //                     ` : `
            //                         <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
            //                              style="width: 60px; height: 60px; font-size: 1.8rem; font-weight: bold;">
            //                             ${data.user.name.charAt(0).toUpperCase()}
            //                         </div>
            //                     `}
            //                     <div>
            //                         <h5 class="mb-0">${data.user.name}</h5>
            //                         <p class="text-muted mb-0">
            //                             <i class="fas fa-envelope me-1"></i> ${data.user.email}
            //                         </p>
            //                     </div>
            //                 </div>
            //             </div>
            //         </div>
            //     `;
            // }

            // ✅ Tampilkan statistik aktivitas
            if (data.activities && data.activities.length > 0) {
                const reportCount = data.activities.filter(a => a.type === 'Report Activity').length;
                const competitorCount = data.activities.filter(a => a.type === 'Report Competitor').length;
                const operationalCount = data.activities.filter(a => a.type === 'Report Operational').length;

                html += `
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                    <h4 class="mb-0">${reportCount}</h4>
                                    <small>Report Activity</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h4 class="mb-0">${competitorCount}</h4>
                                    <small>Report Competitor</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-2x mb-2"></i>
                                    <h4 class="mb-0">${operationalCount}</h4>
                                    <small>Report Operational</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // ✅ Tampilkan timeline aktivitas
                html += '<div class="timeline">';

                data.activities.forEach((activity, index) => {
                    let badgeClass = 'bg-primary';
                    let iconClass = 'fa-clipboard-list';

                    if (activity.type === 'Report Competitor') {
                        badgeClass = 'bg-danger';
                        iconClass = 'fa-users';
                    } else if (activity.type === 'Report Operational') {
                        badgeClass = 'bg-success';
                        iconClass = 'fa-user-plus';
                    }

                    const statusBadge = activity.status === 'selesai'
                        ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Selesai</span>'
                        : '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Proses</span>';

                    html += `
                        <div class="timeline-item mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="timeline-badge ${badgeClass} rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas ${iconClass} text-white"></i>
                                            </div>
                                            <div>
                                                <span class="badge ${badgeClass} mb-1">${activity.type}</span>
                                                <h6 class="mb-0">
                                                    <i class="fas fa-calendar-day text-primary me-2"></i>
                                                    ${activity.date}
                                                    <small class="text-muted">(${activity.day})</small>
                                                </h6>
                                            </div>
                                        </div>
                                        <div>
                                            ${statusBadge}
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <strong><i class="fas fa-tasks text-info me-2"></i>Aktivitas:</strong>
                                        <p class="mb-0 ms-4">${activity.activity}</p>
                                    </div>
                                    <div class="mb-2">
                                        <strong><i class="fas fa-map-marker-alt text-danger me-2"></i>Lokasi:</strong>
                                        <span class="badge bg-secondary ms-2">${activity.location}</span>
                                    </div>
                                    ${activity.hasil_kendala && activity.hasil_kendala !== '-' ? `
                                        <div class="mb-2">
                                            <strong><i class="fas fa-clipboard-check text-success me-2"></i>Hasil/Kendala:</strong>
                                            <p class="mb-0 ms-4 text-muted">${activity.hasil_kendala}</p>
                                        </div>
                                    ` : ''}
                                    ${activity.evidence ? `
                                        <div class="mt-2">
                                            <strong class="d-block mb-2"><i class="fas fa-image text-warning me-2"></i>Evidence:</strong>
                                            <img src="${activity.evidence}"
                                                 alt="Evidence"
                                                 class="img-thumbnail"
                                                 style="max-width: 200px; cursor: pointer;"
                                                 onclick="window.open('${activity.evidence}', '_blank')">
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
            } else {
                html += `
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada aktivitas bulan ini</p>
                    </div>
                `;
            }

            document.getElementById('salesDetailContent').innerHTML = html;
        } else {
            document.getElementById('salesDetailContent').innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> ${data.message || 'Data tidak ditemukan'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('salesDetailContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Gagal memuat data: ${error.message}
            </div>
        `;
    });
}

// ✅ Hover effect untuk sales cards
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hover-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 20px rgba(0,0,0,0.15)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
});
</script>

{{-- CountUp.js --}}
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.6.2/dist/countUp.umd.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const elements = ['countSales', 'countPelanggan', 'countUsers'];
    elements.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            const endValue = parseInt(el.textContent.replace(/[^\d]/g, '')) || 0;
            const counter = new countUp.CountUp(id, endValue, { duration: 2, separator: '.' });
            if (!counter.error) counter.start();
        }
    });
});
</script>

{{-- Dropdown interaktif --}}
<script>
function toggleDropdown(dropdownId, event) {
    if (event) event.stopPropagation();

    // Tutup semua dropdown kecuali yang diklik
    document.querySelectorAll('.card-dropdown').forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.remove('show');
            d.parentElement.style.zIndex = '';
        }
    });

    // Toggle dropdown yang diklik
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('show');
        dropdown.parentElement.style.zIndex = dropdown.classList.contains('show') ? '9999' : '';
    }
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function(e) {
    if (!e.target.closest('.card-clickable')) {
        document.querySelectorAll('.card-dropdown').forEach(d => {
            d.classList.remove('show');
            d.parentElement.style.zIndex = '';
        });
    }
});
</script>


@endsection