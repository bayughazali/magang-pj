@extends('layouts.app')

@section('content')
<style>
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
    .card-dropdown.show { display: block; }
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
    .card-clickable { cursor: pointer; user-select: none; }
    .chart-card {
<<<<<<< Updated upstream
=======
    border-radius: 14px;
    padding: 20px;
    min-height: 450px; /* 🟢 Ubah dari height ke min-height */
    display: flex;
    flex-direction: column;
}

/* 🟢 Tambahkan wrapper untuk canvas */
.chart-wrapper {
    position: relative;
    flex: 1;
    min-height: 350px;
}

.chart-wrapper canvas {
    max-height: 100%;
}
    .table-card {
>>>>>>> Stashed changes
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

        <div class="row mt-4">
            {{-- SALES REPORT --}}
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
                                    Total laporan bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
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

            {{-- OPERATIONAL REPORT --}}
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
                                    Data pelanggan bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
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
                    <div class="card-dropdown" id="userDropdown">
                        <a href="{{ url('/user') }}"><i class="ni ni-single-02"></i> Daftar User</a>
                    </div>
                </div>
<<<<<<< Updated upstream
            </div>
=======
            </div> --}}


>>>>>>> Stashed changes

            {{-- EXPORT DATA --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card card-stats border-0 shadow-sm bg-white card-clickable"
                     onclick="toggleDropdown('exportDropdown')">
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
        </div>
    </div>
</div>

{{-- BAGIAN CHART --}}
<div class="container-fluid mt-n5">
    <div class="row">
        {{-- LINE CHART --}}
        <div class="col-xl-7">
            <div class="card chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-line text-primary me-2"></i> Tren Pertumbuhan Pelanggan
                </div>
                <div class="chart-subtitle">
                    Grafik menunjukkan total akumulasi pelanggan dalam 12 bulan terakhir
                </div>
                <div class="chart-container"><canvas id="chart-line"></canvas></div>
            </div>
        </div>

        {{-- BAR CHART --}}
        <div class="col-xl-5">
            <div class="card chart-card">
                <div class="chart-title">
                    <i class="fas fa-map-marked-alt text-info me-2"></i> Pelanggan per Provinsi Bulan Ini
                </div>
                <div class="chart-subtitle">Distribusi pelanggan baru berdasarkan provinsi</div>
                <div class="chart-container"><canvas id="chart-bar"></canvas></div>
            </div>
=======
{{-- ============================================ --}}
{{-- BAGIAN CHART DAN CAROUSEL --}}
{{-- ============================================ --}}
<div class="container-fluid mt-n5">
    <div class="row">

        {{-- CAROUSEL PROMO/INFORMASI DENGAN TAMPILAN LEBIH MENARIK --}}
        {{-- <div class="col-xl-7">
            <div class="card chart-card" style="overflow: hidden;">
                <div class="chart-title">
                    <i class="fas fa-bullhorn text-warning me-2"></i>
                    Promo & Informasi Terbaru
                </div>
                <div class="chart-subtitle">
                    <i class="fas fa-hand-pointer text-primary"></i>
                    Geser untuk melihat promo dan informasi lainnya
                </div>

                Carousel Container
                <div class="carousel-container"> --}}
                    {{-- Progress Bar --}}
                    {{-- <div class="carousel-progress" id="carouselProgress"></div>

                    <button class="carousel-btn carousel-btn-prev" onclick="moveSlide(-1)" aria-label="Previous Slide">
                        <i class="fas fa-chevron-left"></i>
                    </button>

                    <div class="carousel-wrapper">
                        <div class="carousel-track" id="carouselTrack"> --}}
                            {{-- Slide 1 - Promo Akhir Tahun --}}
                            {{-- <div class="carousel-slide">
                                <span class="promo-badge">HOT PROMO</span>
                                <img src="{{ asset('images/promo/promo1.jpg') }}" alt="Promo 1" onerror="this.src='https://via.placeholder.com/800x400/4c6ef5/ffffff?text=Promo+Spesial+Akhir+Tahun'">
                                <div class="carousel-caption">
                                    <h5>🎉 Promo Akhir Tahun Spektakuler!</h5>
                                    <p>Dapatkan diskon hingga 50% untuk semua produk pilihan. Buruan sebelum kehabisan!</p>
                                </div>
                            </div> --}}

                            {{-- Slide 2 - Update Sistem --}}
                            {{-- <div class="carousel-slide">
                                <span class="promo-badge info-badge">INFO</span>
                                <img src="{{ asset('images/promo/promo2.jpg') }}" alt="Promo 2" onerror="this.src='https://via.placeholder.com/800x400/28a745/ffffff?text=Update+Sistem+Terbaru'">
                                <div class="carousel-caption">
                                    <h5>🚀 Update Sistem Terbaru V2.0</h5>
                                    <p>Nikmati fitur-fitur baru yang lebih canggih dan mudah digunakan di dashboard Anda</p>
                                </div>
                            </div> --}}

                            {{-- Slide 3 - Bundling --}}
                            {{-- <div class="carousel-slide">
                                <span class="promo-badge new-badge">NEW</span>
                                <img src="{{ asset('images/promo/promo3.jpg') }}" alt="Promo 3" onerror="this.src='https://via.placeholder.com/800x400/dc3545/ffffff?text=Paket+Bundling+Hemat'">
                                <div class="carousel-caption">
                                    <h5>💎 Paket Bundling Super Hemat</h5>
                                    <p>Beli 2 gratis 1 untuk produk pilihan. Penawaran terbatas hanya untuk Anda!</p>
                                </div>
                            </div> --}}

                            {{-- Slide 4 - Cashback --}}
                            {{-- <div class="carousel-slide">
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
                </div> --}}

                {{-- Carousel Indicators --}}
                {{-- <div class="carousel-indicators" id="carouselIndicators"></div>
            </div>
        </div> --}}

        {{-- GRAFIK BAR: Pelanggan per Provinsi --}}
        {{-- Bagian Chart dan Table --}}
<div class="col-xl-7">
    <div class="card chart-card" style="min-height: 500px; padding: 20px;">
        <div class="chart-title">
            <i class="fas fa-chart-line text-primary me-2"></i>
            Tren Pertumbuhan Pelanggan
        </div>
        <div class="chart-subtitle">
            Grafik menunjukkan total akumulasi pelanggan dalam 12 bulan terakhir
        </div>
        <div style="position: relative; height: 400px;">
            <canvas id="chart-line"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
<div class="col-xl-5">
    <div class="card chart-card" style="min-height: 500px; padding: 20px;">
        <div class="chart-title">
            <i class="fas fa-map-marked-alt text-info me-2"></i>
            Pelanggan per Provinsi Bulan Ini
        </div>
        <div class="chart-subtitle">
            Distribusi pelanggan baru berdasarkan provinsi
        </div>
        <div style="position: relative; height: 400px;">
            <canvas id="chart-bar"></canvas>
        </div>
    </div>
</div>
    {{-- TABLE --}}
    {{-- <div class="card table-card mt-4">
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
</div> --}}

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ========================================
    // DATA DARI CONTROLLER
    // ========================================
    const bulanLabels = {!! json_encode($bulanLabels ?? []) !!};
    const pelangganTren = {!! json_encode($pelangganTren ?? []) !!};
    const clusterLabels = {!! json_encode($clusterLabels ?? []) !!};
    const clusterValues = {!! json_encode($clusterValues ?? []) !!};

    if (bulanLabels.length === 0 || pelangganTren.length === 0) {
        console.warn("Data tren pelanggan kosong, grafik tidak dirender.");
        return;
    }

    // LINE CHART
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
    // DEBUG: Tampilkan data di console
    console.log("=== DATA GRAFIK DASHBOARD ===");
    console.log("Bulan Labels:", bulanLabels);
    console.log("Pelanggan Tren:", pelangganTren);
    console.log("Cluster Labels:", clusterLabels);
    console.log("Cluster Values:", clusterValues);

    // ========================================
    // LINE CHART: Tren Pertumbuhan Pelanggan
    // ========================================
    const ctxLine = document.getElementById('chart-line');
    if (ctxLine) {
        new Chart(ctxLine, {
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
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#4c6ef5',
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
                        labels: {
                            font: { size: 12 },
                            color: '#2c3e50',
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return 'Total Pelanggan: ' + context.parsed.y + ' pelanggan';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value;
                            },
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
                            color: '#6c757d',
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // ========================================
    // BAR CHART: Pelanggan per Provinsi
    // ========================================
    const ctxBar = document.getElementById('chart-bar');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: clusterLabels,
                datasets: [{
                    label: 'Jumlah Pelanggan',
                    data: clusterValues,
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
<<<<<<< Updated upstream
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // BAR CHART (batang tebal)
    new Chart(document.getElementById('chart-bar'), {
        type: 'bar',
        data: {
            labels: clusterLabels,
            datasets: [{
                label: 'Jumlah Pelanggan',
                data: clusterValues,
                backgroundColor: ['#4c6ef5','#6a92ff','#28a745','#ffc107','#dc3545','#17a2b8'],
                borderRadius: 10,
                barThickness: 50, // ✅ batang lebih tebal
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
});
=======
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
    }
>>>>>>> Stashed changes
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
    document.querySelectorAll('.card-dropdown').forEach(d => {
        if (d.id !== dropdownId) { d.classList.remove('show'); d.parentElement.style.zIndex = ''; }
    });
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('show');
        dropdown.parentElement.style.zIndex = dropdown.classList.contains('show') ? 9999 : '';
    }
}
document.addEventListener('click', e => {
    if (!e.target.closest('.card-clickable')) {
        document.querySelectorAll('.card-dropdown').forEach(d => {
            d.classList.remove('show');
            d.parentElement.style.zIndex = '';
        });
    }
});
</script>
<<<<<<< Updated upstream
@endsection
=======

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
   // ✅ KODE BARU (DENGAN PENGECEKAN)
function createIndicators() {
    const indicatorsContainer = document.getElementById('carouselIndicators');

    // 🟢 Tambahkan pengecekan ini
    if (!indicatorsContainer) {
        console.warn('Carousel indicators container not found');
        return; // Keluar dari fungsi jika elemen tidak ada
    }

    indicatorsContainer.innerHTML = '';
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

        // Update progress barfarengga
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
    // ✅ KODE BARU (DENGAN PENGECEKAN LENGKAP)

document.addEventListener('DOMContentLoaded', function() {
    // 🟢 Cek apakah carousel ada sebelum inisialisasi
    const carouselContainer = document.querySelector('.carousel-container');

    if (carouselContainer) {
        // Hanya jalankan jika carousel ada
        createIndicators();
        startAutoSlide();
        updateProgressBar();

        // Pause auto slide saat hover
        carouselContainer.addEventListener('mouseenter', () => {
            clearInterval(autoSlideInterval);
            const progressBar = document.getElementById('carouselProgress');
            if (progressBar) progressBar.style.animationPlayState = 'paused';
        });

        carouselContainer.addEventListener('mouseleave', () => {
            startAutoSlide();
            const progressBar = document.getElementById('carouselProgress');
            if (progressBar) progressBar.style.animationPlayState = 'running';
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

        // Preload images untuk smooth transition
        const slides = document.querySelectorAll('.carousel-slide');
        slides.forEach((slide, index) => {
            const img = slide.querySelector('img');
            if (img && !img.complete) {
                slide.classList.add('loading');
                img.addEventListener('load', () => {
                    slide.classList.remove('loading');
                });
            }
        });
    } else {
        console.info('Carousel not found, skipping initialization');
    }
}); // ← Ini penutup document.addEventListener
</script>

@endsection
>>>>>>> Stashed changes
