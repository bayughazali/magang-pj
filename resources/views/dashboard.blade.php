@extends('layouts.app')

@section('content')
<style>
    .header-bg {
        background: linear-gradient(90deg, #4c6ef5, #6a92ff);
        padding: 30px 0 120px 0;
        border-radius: 0 0 18px 18px;
        overflow: visible;
    }
    .card-stats {
        border-radius: 14px;
        transition: .2s;
        position: relative;
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
        z-index: 1000;
        display: none;
        margin-top: -14px;
        padding-top: 14px;
    }
    .card-dropdown.show {
        display: block;
    }
    .card-dropdown a {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    .card-dropdown a:last-child {
        border-bottom: none;
    }
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
        height: 320px;
    }
    .table-card {
        border-radius: 14px;
    }
    
    /* Chart Title Styling */
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

    /* ============================================ */
    /* CAROUSEL STYLES - TAMPILAN LEBIH MENARIK */
    /* ============================================ */
    .carousel-container {
        position: relative;
        width: 100%;
        height: 220px;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .carousel-wrapper {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    
    .carousel-track {
        display: flex;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }
    
    .carousel-slide {
        min-width: 100%;
        height: 100%;
        position: relative;
        flex-shrink: 0;
    }
    
    .carousel-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.85);
        transition: filter 0.3s, transform 0.3s;
    }
    
    .carousel-slide:hover img {
        filter: brightness(0.95);
        transform: scale(1.05);
    }
    
    /* Gradient Overlay yang lebih dinamis */
    .carousel-slide::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            135deg, 
            rgba(76, 110, 245, 0.3) 0%,
            rgba(0, 0, 0, 0.2) 50%,
            rgba(0, 0, 0, 0.8) 100%
        );
        z-index: 1;
        transition: opacity 0.3s;
    }
    
    .carousel-slide:hover::before {
        opacity: 0.7;
    }
    
    /* Caption dengan animasi yang lebih menarik */
    .carousel-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.95), rgba(0,0,0,0.7) 70%, transparent);
        color: white;
        padding: 25px 25px 20px;
        z-index: 2;
        transform: translateY(0);
        transition: transform 0.3s;
    }
    
    .carousel-slide:hover .carousel-caption {
        transform: translateY(-5px);
    }
    
    .carousel-caption h5 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 8px;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        letter-spacing: 0.5px;
        animation: slideInUp 0.5s ease-out;
    }
    
    .carousel-caption p {
        font-size: 0.9rem;
        margin: 0;
        opacity: 0.95;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
        line-height: 1.5;
        animation: slideInUp 0.7s ease-out;
    }
    
    /* Badge untuk label promo */
    .promo-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #ff6b6b, #ff8787);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        z-index: 3;
        box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        animation: pulse 2s infinite;
    }
    
    .info-badge {
        background: linear-gradient(135deg, #4c6ef5, #6a92ff);
        box-shadow: 0 4px 12px rgba(76, 110, 245, 0.4);
    }
    
    .new-badge {
        background: linear-gradient(135deg, #28a745, #48c774);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Tombol navigasi dengan desain modern */
    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.95);
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        backdrop-filter: blur(5px);
    }
    
    .carousel-btn:hover {
        background: white;
        box-shadow: 0 6px 25px rgba(76, 110, 245, 0.4);
        transform: translateY(-50%) scale(1.15);
    }
    
    .carousel-btn:active {
        transform: translateY(-50%) scale(0.95);
    }
    
    .carousel-btn-prev {
        left: 15px;
    }
    
    .carousel-btn-next {
        right: 15px;
    }
    
    .carousel-btn i {
        color: #4c6ef5;
        font-size: 1.3rem;
        transition: transform 0.3s;
    }
    
    .carousel-btn:hover i {
        transform: scale(1.2);
    }
    
    /* Indicators dengan desain yang lebih menarik */
    .carousel-indicators {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 18px;
    }
    
    .indicator-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #e0e0e0;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .indicator-dot::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: white;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .indicator-dot.active {
        background: linear-gradient(135deg, #4c6ef5, #6a92ff);
        width: 32px;
        border-radius: 6px;
        box-shadow: 0 3px 10px rgba(76, 110, 245, 0.5);
    }
    
    .indicator-dot.active::before {
        opacity: 0.3;
    }
    
    .indicator-dot:hover {
        background: #6a92ff;
        transform: scale(1.2);
    }
    
    /* Progress bar untuk auto-slide */
    .carousel-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #4c6ef5, #6a92ff);
        width: 0%;
        transition: width 0.1s linear;
        z-index: 4;
        box-shadow: 0 0 10px rgba(76, 110, 245, 0.5);
    }
    
    .carousel-progress.active {
        animation: progressBar 5s linear;
    }
    
    @keyframes progressBar {
        from { width: 0%; }
        to { width: 100%; }
    }
    
    /* Efek shimmer saat loading */
    .carousel-slide.loading {
        background: linear-gradient(
            90deg,
            #f0f0f0 25%,
            #e0e0e0 50%,
            #f0f0f0 75%
        );
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<div class="header-bg">
    <div class="container-fluid">
        <h2 class="text-white mb-3">Dashboard</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home text-white"></i></a></li>
                <li class="breadcrumb-item text-white">Dashboards</li>
                <li class="breadcrumb-item active text-white">Default</li>
            </ol>
        </nav>

        <div class="row mt-4">

            {{-- SALES REPORT CARD --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0 shadow-sm bg-white card-clickable" 
                     onclick="toggleDropdown('salesDropdown')">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Sales Report</h6>
                                <h2 class="font-weight-bold mb-0 text-dark" id="countSales">
                                    {{ number_format($totalReportBulanIni ?? 0) }}
                                </h2>
                                <small class="text-muted">
                                    Total laporan bulan 
                                    <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
                                </small>
                                <p class="mt-3 mb-0 text-sm">
                                    <span class="{{ $persenSales >= 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fa fa-arrow-{{ $persenSales >= 0 ? 'up' : 'down' }}"></i> 
                                        {{ number_format(abs($persenSales), 2) }}%
                                    </span> 
                                    dibanding bulan lalu
                                </p>
                            </div>
                            <div>
                                <i class="ni ni-chart-bar-32 display-4 text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-dropdown" id="salesDropdown">
                        <a href="{{ route('reports.activity') }}">
                            <i class="ni ni-bullet-list-67"></i> Report Activity
                        </a>
                        <a href="{{ route('reports.competitor') }}">
                            <i class="ni ni-chart-pie-35"></i> Report Competitor
                        </a>
                    </div>
                </div>
            </div>

            {{-- OPERATIONAL REPORT CARD --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0 shadow-sm bg-white card-clickable" 
                     onclick="toggleDropdown('operationalDropdown')">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Operational Report</h6>
                                <h2 class="font-weight-bold mb-0 text-dark" id="countPelanggan">
                                    {{ number_format($totalPelangganBulanIni ?? 0) }}
                                </h2>
                                <small class="text-muted">
                                    Data pelanggan bulan 
                                    <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
                                </small>
                                <p class="mt-3 mb-0 text-sm">
                                    <span class="{{ $persenPelanggan >= 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fa fa-arrow-{{ $persenPelanggan >= 0 ? 'up' : 'down' }}"></i> 
                                        {{ number_format(abs($persenPelanggan), 2) }}%
                                    </span> 
                                    dibanding bulan lalu
                                </p>
                            </div>
                            <div>
                                <i class="ni ni-laptop display-4 text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-dropdown" id="operationalDropdown">
                        <a href="{{ route('report.operational.index') }}">
                            <i class="ni ni-archive-2"></i> Input Data Pelanggan
                        </a>
                        <a href="{{ route('report.customer.search') }}">
                            <i class="ni ni-zoom-split-in"></i> Cari Pelanggan & Kode FAT
                        </a>
                    </div>
                </div>
            </div>

            {{-- USER MANAGEMENT CARD --}}
            {{-- <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0 shadow-sm bg-white card-clickable" 
                     onclick="toggleDropdown('userDropdown')">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">User Management</h6>
                                <h2 class="font-weight-bold mb-0 text-dark" id="countUsers">
                                    {{ number_format($totalUsers ?? 0) }}
                                </h2>
                                <small class="text-muted">
                                    Total user terdaftar
                                </small>
                                <p class="mt-3 mb-0 text-sm">
                                    <span class="{{ $persenUsers >= 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fa fa-arrow-{{ $persenUsers >= 0 ? 'up' : 'down' }}"></i> 
                                        {{ number_format(abs($persenUsers), 2) }}%
                                    </span> 
                                    user baru bulan ini
                                </p>
                            </div>
                            <div>
                                <i class="ni ni-single-02 display-4 text-info"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-dropdown" id="userDropdown">
                        <a href="{{ route('users.index') }}">
                            <i class="ni ni-single-02"></i> Daftar User
                        </a>
                    </div>
                </div>
            </div> --}}

            {{-- USER MANAGEMENT CARD --}}
<div class="col-xl-3 col-sm-6">
    <div class="card card-stats border-0 shadow-sm bg-white card-clickable" 
         onclick="toggleDropdown('userDropdown')">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase text-muted mb-1">User Management</h6>
                    <h2 class="font-weight-bold mb-0 text-dark" id="countUsers">
                        {{ number_format($totalUsers ?? 0) }}
                    </h2>
                    <small class="text-muted">
                        Total user terdaftar
                    </small>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="{{ $persenUsers >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fa fa-arrow-{{ $persenUsers >= 0 ? 'up' : 'down' }}"></i> 
                            {{ number_format(abs($persenUsers), 2) }}%
                        </span> 
                        user baru bulan ini
                    </p>
                </div>
                <div>
                    <i class="ni ni-single-02 display-4 text-info"></i>
                </div>
            </div>
        </div>

        {{-- 🔽 Tambahkan Dropdown ini (kalau belum ada) --}}
        <div class="card-dropdown" id="userDropdown">
            <a href="{{ url('/users') }}">
                <i class="ni ni-single-02"></i> Daftar User
            </a>
        </div>
    </div>
</div>


            {{-- EXPORT DATA CARD --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0 shadow-sm bg-white card-clickable" 
                     onclick="toggleDropdown('exportDropdown')">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Export Data</h6>
                                <h2 class="font-weight-bold mb-0 text-dark" id="countExport">
                                    <i class="ni ni-cloud-download-95"></i>
                                </h2>
                                <small class="text-muted">
                                    Download laporan
                                </small>
                                <p class="mt-3 mb-0 text-sm text-muted">
                                    Klik untuk pilihan export
                                </p>
                            </div>
                            <div>
                                <i class="ni ni-archive-2 display-4 text-purple"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-dropdown" id="exportDropdown">
                        <a href="{{ route('export.activity') }}">
                            <i class="ni ni-single-copy-04"></i> Report Activity
                        </a>
                        <a href="{{ route('export.competitor') }}">
                            <i class="ni ni-single-copy-04"></i> Report Competitor
                        </a>
                        <a href="{{ route('export.operational') }}">
                            <i class="ni ni-single-copy-04"></i> Report Operational
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ============================================ --}}
{{-- BAGIAN CHART DAN CAROUSEL --}}
{{-- ============================================ --}}
<div class="container-fluid mt-n5">
    <div class="row">

        {{-- CAROUSEL PROMO/INFORMASI DENGAN TAMPILAN LEBIH MENARIK --}}
        <div class="col-xl-7">
            <div class="card chart-card" style="overflow: hidden;">
                <div class="chart-title">
                    <i class="fas fa-bullhorn text-warning me-2"></i>
                    Promo & Informasi Terbaru
                </div>
                <div class="chart-subtitle">
                    <i class="fas fa-hand-pointer text-primary"></i>
                    Geser untuk melihat promo dan informasi lainnya
                </div>
                
                {{-- Carousel Container --}}
                <div class="carousel-container">
                    {{-- Progress Bar --}}
                    <div class="carousel-progress" id="carouselProgress"></div>
                    
                    <button class="carousel-btn carousel-btn-prev" onclick="moveSlide(-1)" aria-label="Previous Slide">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <div class="carousel-wrapper">
                        <div class="carousel-track" id="carouselTrack">
                            {{-- Slide 1 - Promo Akhir Tahun --}}
                            <div class="carousel-slide">
                                <span class="promo-badge">HOT PROMO</span>
                                <img src="{{ asset('images/promo/promo1.jpg') }}" alt="Promo 1" onerror="this.src='https://via.placeholder.com/800x400/4c6ef5/ffffff?text=Promo+Spesial+Akhir+Tahun'">
                                <div class="carousel-caption">
                                    <h5>🎉 Promo Akhir Tahun Spektakuler!</h5>
                                    <p>Dapatkan diskon hingga 50% untuk semua produk pilihan. Buruan sebelum kehabisan!</p>
                                </div>
                            </div>
                            
                            {{-- Slide 2 - Update Sistem --}}
                            <div class="carousel-slide">
                                <span class="promo-badge info-badge">INFO</span>
                                <img src="{{ asset('images/promo/promo2.jpg') }}" alt="Promo 2" onerror="this.src='https://via.placeholder.com/800x400/28a745/ffffff?text=Update+Sistem+Terbaru'">
                                <div class="carousel-caption">
                                    <h5>🚀 Update Sistem Terbaru V2.0</h5>
                                    <p>Nikmati fitur-fitur baru yang lebih canggih dan mudah digunakan di dashboard Anda</p>
                                </div>
                            </div>
                            
                            {{-- Slide 3 - Bundling --}}
                            <div class="carousel-slide">
                                <span class="promo-badge new-badge">NEW</span>
                                <img src="{{ asset('images/promo/promo3.jpg') }}" alt="Promo 3" onerror="this.src='https://via.placeholder.com/800x400/dc3545/ffffff?text=Paket+Bundling+Hemat'">
                                <div class="carousel-caption">
                                    <h5>💎 Paket Bundling Super Hemat</h5>
                                    <p>Beli 2 gratis 1 untuk produk pilihan. Penawaran terbatas hanya untuk Anda!</p>
                                </div>
                            </div>
                            
                            {{-- Slide 4 - Cashback --}}
                            <div class="carousel-slide">
                                <span class="promo-badge">CASHBACK</span>
                                <img src="{{ asset('images/promo/promo4.jpg') }}" alt="Promo 4" onerror="this.src='https://via.placeholder.com/800x400/ffc107/333333?text=Cashback+20%25'">
                                <div class="carousel-caption">
                                    <h5>💰 Cashback 20% Langsung!</h5>
                                    <p>Untuk setiap transaksi minimal Rp 500.000. Semakin banyak belanja, semakin untung!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button class="carousel-btn carousel-btn-next" onclick="moveSlide(1)" aria-label="Next Slide">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                {{-- Carousel Indicators --}}
                <div class="carousel-indicators" id="carouselIndicators"></div>
            </div>
        </div>

        {{-- GRAFIK BAR: Pelanggan per Provinsi --}}
        <div class="col-xl-5">
            <div class="card chart-card">
                <div class="chart-title">
                    <i class="fas fa-map-marked-alt text-info me-2"></i>
                    Pelanggan per Provinsi Bulan Ini
                </div>
                <div class="chart-subtitle">
                    Distribusi pelanggan baru berdasarkan provinsi
                </div>
                <canvas id="chart-bar"></canvas>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card table-card mt-4">
        <div class="table-responsive p-3">
            <table class="table align-items-center">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Arengga</td>
                        <td>Admin</td>
                        <td>2024-10-23</td>
                        <td class="text-right"><a href="#" class="btn btn-sm btn-primary">Details</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    console.log("bulanLabels:", {!! json_encode($bulanLabels) !!});
    console.log("pelangganTren:", {!! json_encode($pelangganTren) !!});
    console.log("clusterLabels:", {!! json_encode($clusterLabels) !!});
    console.log("clusterValues:", {!! json_encode($clusterValues) !!});
</script>

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // BAR CHART: Pelanggan per Provinsi Bulan Ini
    new Chart(document.getElementById('chart-bar'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($clusterLabels) !!},
            datasets: [{
                label: 'Jumlah Pelanggan',
                data: {!! json_encode($clusterValues) !!},
                backgroundColor: [
                    '#4c6ef5',
                    '#6a92ff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8'
                ],
                borderRadius: 6,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            return 'Pelanggan: ' + context.parsed.y + ' orang';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 11 },
                        color: '#6c757d'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: { size: 10 },
                        color: '#6c757d'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>

{{-- CountUp.js untuk animasi angka --}}
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.6.2/dist/countUp.umd.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const elements = ['countSales', 'countPelanggan', 'countUsers'];

    elements.forEach(function(id) {
        const el = document.getElementById(id);
        if (el) {
            const endValue = parseInt(el.textContent.replace(/[^\d]/g, '')) || 0;
            const counter = new countUp.CountUp(id, endValue, {
                duration: 2,
                separator: '.'
            });
            if (!counter.error) {
                counter.start();
            } else {
                console.error(counter.error);
            }
        }
    });
});
</script>

{{-- Dropdown interaktif --}}
<script>
function toggleDropdown(dropdownId, event) {
    if (event) event.stopPropagation();

    document.querySelectorAll('.card-dropdown').forEach(function(d) {
        if (d.id !== dropdownId) d.classList.remove('show');
    });

    const dropdown = document.getElementById(dropdownId);
    if (dropdown) dropdown.classList.toggle('show');
}

document.addEventListener('click', function(event) {
    if (!event.target.closest('.card-clickable')) {
        document.querySelectorAll('.card-dropdown').forEach(function(d) {
            d.classList.remove('show');
        });
    }
});

document.querySelectorAll('.card-dropdown a').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>

{{-- ============================================ --}}
{{-- CAROUSEL JAVASCRIPT DENGAN FITUR LENGKAP --}}
{{-- ============================================ --}}
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const totalSlides = slides.length;
    let autoSlideInterval;
    let progressInterval;
    
    // Buat indicator dots
    function createIndicators() {
        const indicatorsContainer = document.getElementById('carouselIndicators');
        indicatorsContainer.innerHTML = ''; // Clear existing
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('div');
            dot.className = 'indicator-dot';
            if (i === 0) dot.classList.add('active');
            dot.onclick = () => goToSlide(i);
            dot.setAttribute('aria-label', `Slide ${i + 1}`);
            indicatorsContainer.appendChild(dot);
        }
    }
    
    // Update progress bar
    function updateProgressBar() {
        const progressBar = document.getElementById('carouselProgress');
        progressBar.classList.remove('active');
        // Trigger reflow
        void progressBar.offsetWidth;
        progressBar.classList.add('active');
    }
    
    // Pindah ke slide tertentu
    function goToSlide(n) {
        currentSlide = n;
        if (currentSlide >= totalSlides) currentSlide = 0;
        if (currentSlide < 0) currentSlide = totalSlides - 1;
        
        const track = document.getElementById('carouselTrack');
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        // Update indicators
        document.querySelectorAll('.indicator-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
        
        // Update progress bar
        updateProgressBar();
        
        // Reset auto slide
        resetAutoSlide();
    }
    
    // Pindah slide (next/prev)
    function moveSlide(direction) {
        goToSlide(currentSlide + direction);
    }
    
    // Auto slide setiap 5 detik
    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            moveSlide(1);
        }, 5000);
    }
    
    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }
    
    // Inisialisasi carousel
    document.addEventListener('DOMContentLoaded', function() {
        createIndicators();
        startAutoSlide();
        updateProgressBar();
        
        const carouselContainer = document.querySelector('.carousel-container');
        if (carouselContainer) {
            // Pause auto slide saat hover
            carouselContainer.addEventListener('mouseenter', () => {
                clearInterval(autoSlideInterval);
                const progressBar = document.getElementById('carouselProgress');
                progressBar.style.animationPlayState = 'paused';
            });
            
            carouselContainer.addEventListener('mouseleave', () => {
                startAutoSlide();
                const progressBar = document.getElementById('carouselProgress');
                progressBar.style.animationPlayState = 'running';
            });
            
            // Touch/Swipe support untuk mobile
            let touchStartX = 0;
            let touchEndX = 0;
            let touchStartY = 0;
            let touchEndY = 0;
            
            carouselContainer.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
            }, { passive: true });
            
            carouselContainer.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                touchEndY = e.changedTouches[0].screenY;
                handleSwipe();
            }, { passive: true });
            
            function handleSwipe() {
                const deltaX = touchEndX - touchStartX;
                const deltaY = touchEndY - touchStartY;
                
                // Hanya swipe horizontal jika lebih dominan dari vertical
                if (Math.abs(deltaX) > Math.abs(deltaY)) {
                    if (deltaX < -50) {
                        moveSlide(1); // Swipe left
                    } else if (deltaX > 50) {
                        moveSlide(-1); // Swipe right
                    }
                }
            }
            
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    moveSlide(-1);
                } else if (e.key === 'ArrowRight') {
                    moveSlide(1);
                }
            });
        }
        
        // Preload images untuk smooth transition
        slides.forEach((slide, index) => {
            const img = slide.querySelector('img');
            if (img && !img.complete) {
                slide.classList.add('loading');
                img.addEventListener('load', () => {
                    slide.classList.remove('loading');
                });
            }
        });
    });
</script>

@endsection