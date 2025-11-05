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
                     onclick="toggleDropdown('salesDropdown', event)">
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
                     onclick="toggleDropdown('operationalDropdown', event)">
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
                     onclick="toggleDropdown('userDropdown', event)">
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
                        <a href="{{ url('/user') }}"><i class="ni ni-single-02"></i> User</a>
                        <a href="{{ route('admins.index') }}"><i class="ni ni-badge"></i> Admin</a>
                    </div>
                </div>
            </div>

            {{-- EXPORT DATA --}}
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
                }
            },
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

    console.log('Toggle dropdown:', dropdownId); // Debug log

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
        console.log('Dropdown found:', dropdownId); // Debug log
        dropdown.classList.toggle('show');
        dropdown.parentElement.style.zIndex = dropdown.classList.contains('show') ? '9999' : '';
        console.log('Dropdown has show class:', dropdown.classList.contains('show')); // Debug log
    } else {
        console.error('Dropdown not found:', dropdownId); // Debug log
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
