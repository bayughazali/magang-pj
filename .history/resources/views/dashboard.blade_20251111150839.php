@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 fw-bold">Dashboard Sales Activity</h3>

    <div class="row">
        @foreach ($salesActivities as $sales)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    @if($sales['photo'])
                        <img src="{{ $sales['photo'] }}" class="rounded-circle mb-3" width="70" height="70" alt="photo">
                    @else
                        <div class="bg-{{ $sales['color'] }} text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;font-size:24px;">
                            {{ $sales['initial'] }}
                        </div>
                    @endif
                    <h5 class="fw-bold">{{ $sales['name'] }}</h5>
                    <p class="text-muted small mb-2">{{ $sales['email'] }}</p>
                    <p class="mb-2">Total Aktivitas: <span class="fw-bold">{{ $sales['total'] }}</span></p>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-{{ $sales['color'] }}" role="progressbar" style="width: {{ $sales['progress'] }}%"></div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" onclick="showSalesDetail('{{ $sales['name'] }}')">Detail Aktivitas</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Detail Sales -->
<div class="modal fade" id="salesDetailModal" tabindex="-1" aria-labelledby="salesDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="salesDetailModalLabel">Detail Aktivitas Sales</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Report Activity</h6>
                                <h4 id="reportActivityCount" class="fw-bold text-primary">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Report Competitor</h6>
                                <h4 id="reportCompetitorCount" class="fw-bold text-danger">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Report Operational</h6>
                                <h4 id="reportOperationalCount" class="fw-bold text-success">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-muted">Data di atas merupakan total laporan bulan ini.</p>
            </div>
        </div>
    </div>
</div>

<script>
function showSalesDetail(salesName) {
    fetch(`/dashboard/sales-details?sales=${encodeURIComponent(salesName)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('reportActivityCount').innerText = data.report_activity ?? 0;
                document.getElementById('reportCompetitorCount').innerText = data.report_competitor ?? 0;
                document.getElementById('reportOperationalCount').innerText = data.report_operational ?? 0;
                new bootstrap.Modal(document.getElementById('salesDetailModal')).show();
            }
        })
        .catch(err => console.error(err));
}
</script>
@endsection
