<!-- @extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">Export Report Competitor</h5>
        </div>

        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('export.competitor.pdf') }}" class="btn btn-danger btn-sm me-2">Export PDF</a>
                <a href="{{ route('export.competitor.csv') }}" class="btn btn-success btn-sm">Export CSV</a>
                <a href="{{ route('export.competitor.excel') }}" class="btn btn-primary btn-sm me-2">Export Excel</a>
            </div>

            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="bg-gradient-info text-white">
                        <tr>
                            <th>No</th>
                            <th>Cluster</th>
                            <th>Nama Competitor</th>
                            <th>Paket</th>
                            <th>Kecepatan</th>
                            <th>Kuota</th>
                            <th>Harga</th>
                            <th>Fitur Tambahan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($competitors as $index => $competitor)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $competitor->cluster }}</td>
                                <td>{{ $competitor->competitor_name }}</td>
                                <td>{{ $competitor->paket }}</td>
                                <td>{{ $competitor->kecepatan }}</td>
                                <td>{{ $competitor->kuota }}</td>
                                <td>Rp {{ number_format($competitor->harga, 0, ',', '.') }}</td>
                                <td>{{ $competitor->fitur_tambahan }}</td>
                                <td>{{ $competitor->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data competitor</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection -->
