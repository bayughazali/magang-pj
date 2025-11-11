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
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ✅ BAGIAN CHART --}}
<div class="container-fluid mt-n5">
    <div class="row">
        {{-- ✅ AKTIVITAS SALES REPORT PER USER --}}
        <div class="col-xl-12 mb-4">
            <div class="card chart-card">
                <div class="chart-title">
                    <i class="fas fa-user-tie text-primary me-2"></i>
                    Aktivitas Sales Report Per User - {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </div>
                <div class="chart-subtitle">
                    Klik pada card untuk melihat detail aktivitas sales
                </div>

                @if($salesActivities->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada aktivitas sales bulan ini</p>
                    </div>
                @else
                    <div class="row g-3 mt-2">
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
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
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
            if (data.user) {
                html += `
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                ${data.user.photo ? `
                                    <img src="${data.user.photo}"
                                         alt="${data.user.name}"
                                         class="rounded-circle me-3"
                                         style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                ` : `
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 60px; height: 60px; font-size: 1.8rem; font-weight: bold;">
                                        ${data.user.name.charAt(0).toUpperCase()}
                                    </div>
                                `}
                                <div>
                                    <h5 class="mb-0">${data.user.name}</h5>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-envelope me-1"></i> ${data.user.email}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // ✅ Tampilkan statistik aktivitas
            if (data.activities && data.activities.length > 0) {
                const Count = data.activities.filter(a => a.type === 'Report Activity').length;
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

                // ✅ Tampilkan timeline aktivitas dengan style yang berbeda per tipe
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

    console.log('Toggle dropdown:', dropdownId);

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
        console.log('Dropdown found:', dropdownId);
        dropdown.classList.toggle('show');
        dropdown.parentElement.style.zIndex = dropdown.classList.contains('show') ? '9999' : '';
        console.log('Dropdown has show class:', dropdown.classList.contains('show'));
    } else {
        console.error('Dropdown not found:', dropdownId);
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
