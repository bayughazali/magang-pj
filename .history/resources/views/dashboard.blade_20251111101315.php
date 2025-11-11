@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- CARDS METRICS --}}
    <div class="row mb-4">
        {{-- Sales Report Card --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2" style="font-size: 0.75rem;">SALES REPORT</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalReportsThisMonth }}</h2>
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">Total laporan bulan {{ $currentMonth }}</p>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-bar fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success-subtle text-success">
                            <i class="fas fa-arrow-up"></i> {{ $reportGrowth }}%
                        </span>
                        <span class="text-muted ms-2" style="font-size: 0.85rem;">dibanding bulan lalu</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Operational Report Card --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2" style="font-size: 0.75rem;">OPERATIONAL REPORT</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalPelangganThisMonth }}</h2>
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">Data pelanggan bulan {{ $currentMonth }}</p>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-database fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success-subtle text-success">
                            <i class="fas fa-arrow-up"></i> {{ $pelangganGrowth }}%
                        </span>
                        <span class="text-muted ms-2" style="font-size: 0.85rem;">dibanding bulan lalu</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Management Card --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2" style="font-size: 0.75rem;">USER MANAGEMENT</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalUsers }}</h2>
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">Total user terdaftar</p>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success-subtle text-success">
                            <i class="fas fa-arrow-up"></i> {{ $userGrowth }}%
                        </span>
                        <span class="text-muted ms-2" style="font-size: 0.85rem;">user baru bulan ini</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Export Data Card --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2" style="font-size: 0.75rem;">EXPORT DATA</h6>
                            <h2 class="mb-0 fw-bold"><i class="fas fa-download"></i></h2>
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">Download laporan</p>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-file-export fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Klik untuk pilihan export</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
    <div class="row">
        {{-- ✅ AKTIVITAS SALES REPORT PER USER --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user-tie text-primary me-2"></i>
                        Aktivitas Sales Report Per User - November 2025
                    </h5>
                </div>
                <div class="card-body">
                    @if($salesActivities->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada aktivitas sales bulan ini</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($salesActivities as $sales)
                                <div class="col-md-6">
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

        {{-- Pelanggan per Provinsi --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-map-marked-alt text-primary me-2"></i>
                        Pelanggan per Provinsi Bulan Ini
                    </h5>
                    <small class="text-muted">Distribusi pelanggan baru berdasarkan provinsi</small>
                </div>
                <div class="card-body">
                    <canvas id="provinsiChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Tren Pertumbuhan Pelanggan --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Tren Pertumbuhan Pelanggan
                    </h5>
                    <small class="text-muted">Grafik menunjukkan total akumulasi pelanggan dalam 12 bulan terakhir</small>
                </div>
                <div class="card-body">
                    <canvas id="growthChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ MODAL DETAIL AKTIVITAS SALES --}}
<div class="modal fade" id="salesDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
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

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
        if (data.success && data.activities.length > 0) {
            let html = '<div class="list-group">';

            data.activities.forEach((activity, index) => {
                const statusBadge = activity.status === 'selesai'
                    ? '<span class="badge bg-success">Selesai</span>'
                    : '<span class="badge bg-warning">Proses</span>';

                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">${index + 1}. ${activity.date}</h6>
                                <p class="mb-1">${activity.activity}</p>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> ${activity.location}
                                </small>
                            </div>
                            <div>
                                ${statusBadge}
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            document.getElementById('salesDetailContent').innerHTML = html;
        } else {
            document.getElementById('salesDetailContent').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada aktivitas ditemukan</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('salesDetailContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Gagal memuat data
            </div>
        `;
    });
}

// Chart configurations
document.addEventListener('DOMContentLoaded', function() {
    // Provinsi Chart
    const provinsiData = @json($pelangganPerProvinsi);
    if (provinsiData.length > 0) {
        new Chart(document.getElementById('provinsiChart'), {
            type: 'bar',
            data: {
                labels: provinsiData.map(p => p.provinsi),
                datasets: [{
                    label: 'Jumlah Pelanggan',
                    data: provinsiData.map(p => p.total),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Growth Chart
    const monthlyData = @json($monthlyData);
    new Chart(document.getElementById('growthChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Pelanggan Baru',
                data: monthlyData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});

// Hover effect for sales cards
document.querySelectorAll('.hover-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
    });
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '';
    });
});
</script>
@endsection
