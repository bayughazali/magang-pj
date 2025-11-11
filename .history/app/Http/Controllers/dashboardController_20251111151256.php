@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- 🔹 Header --}}
    <h3 class="mt-4 mb-4 fw-bold text-primary">Dashboard Overview</h3>

    {{-- 🔹 Statistik Cards --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Sales Report</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalReportBulanIni }}</div>
                        <small class="{{ $persenSales >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($persenSales, 1) }}%
                        </small>
                    </div>
                    <div><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Operational Report</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalPelangganBulanIni }}</div>
                        <small class="{{ $persenPelanggan >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($persenPelanggan, 1) }}%
                        </small>
                    </div>
                    <div><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">User Management</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalUsers }}</div>
                        <small class="{{ $persenUsers >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($persenUsers, 1) }}%
                        </small>
                    </div>
                    <div><i class="fas fa-user fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Grafik --}}
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold text-primary">Tren Pelanggan (12 Bulan Terakhir)</div>
                <div class="card-body">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold text-primary">Pelanggan per Provinsi</div>
                <div class="card-body">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Sales Activity per User --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold text-primary">Sales Activity Bulan Ini</div>
        <div class="card-body">
            <div class="row">
                @forelse($salesActivities as $sales)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-{{ $sales['color'] }} shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-3">
                                    @if($sales['photo'])
                                        <img src="{{ $sales['photo'] }}" alt="{{ $sales['name'] }}" class="rounded-circle" width="45" height="45">
                                    @else
                                        <div class="bg-{{ $sales['color'] }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                                            {{ $sales['initial'] }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold">{{ $sales['name'] }}</h6>
                                    <small class="text-muted">{{ $sales['email'] }}</small>
                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $sales['color'] }}" role="progressbar" style="width: {{ $sales['progress'] }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $sales['total'] }} aktivitas</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary ms-2 view-details"
                                    data-sales="{{ $sales['name'] }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Belum ada aktivitas sales bulan ini.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- 🔹 Modal Detail Sales --}}
<div class="modal fade" id="salesDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Detail Aktivitas Sales</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="salesDetailContent" class="p-2">
                    <p class="text-muted text-center">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 🔹 Chart Line
    const lineCtx = document.getElementById('lineChart');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: @json($bulanLabels),
            datasets: [{
                label: 'Jumlah Pelanggan',
                data: @json($pelangganTren),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.2)',
                tension: 0.3,
                fill: true,
            }]
        }
    });

    // 🔹 Chart Bar
    const barCtx = document.getElementById('barChart');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: @json($clusterLabels),
            datasets: [{
                label: 'Jumlah Pelanggan',
                data: @json($clusterValues),
                backgroundColor: '#198754',
            }]
        }
    });

    // 🔹 AJAX Detail Modal
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', async () => {
            const salesName = btn.dataset.sales;
            const modal = new bootstrap.Modal(document.getElementById('salesDetailModal'));
            const content = document.getElementById('salesDetailContent');
            content.innerHTML = `<p class='text-muted text-center'>Memuat data untuk ${salesName}...</p>`;

            try {
                const response = await fetch(`/dashboard/sales-details?sales=${encodeURIComponent(salesName)}`);
                const data = await response.json();

                if (data.success) {
                    let html = `
                        <div class="text-center mb-3">
                            ${data.user?.photo ? `<img src="${data.user.photo}" class="rounded-circle" width="70" height="70">` : ''}
                            <h5 class="mt-2">${data.user?.name ?? salesName}</h5>
                            <p class="text-muted">${data.user?.email ?? ''}</p>
                        </div>
                    `;
                    html += `<ul class="list-group">`;
                    data.activities.forEach(act => {
                        html += `
                            <li class="list-group-item">
                                <strong>${act.type}</strong><br>
                                <small class="text-muted">${act.date} (${act.day})</small>
                                <p class="mb-1">${act.activity}</p>
                                <small><b>Lokasi:</b> ${act.location}</small><br>
                                <small><b>Status:</b> ${act.status}</small><br>
                                <small><b>Hasil/Kendala:</b> ${act.hasil_kendala}</small><br>
                                ${act.evidence ? `<img src="${act.evidence}" class="img-fluid rounded mt-2" style="max-height:150px;">` : ''}
                            </li>
                        `;
                    });
                    html += `</ul>`;
                    content.innerHTML = html;
                } else {
                    content.innerHTML = `<p class='text-danger text-center'>Gagal memuat data sales.</p>`;
                }
            } catch (e) {
                content.innerHTML = `<p class='text-danger text-center'>Terjadi kesalahan: ${e.message}</p>`;
            }

            modal.show();
        });
    });
</script>
@endsection
