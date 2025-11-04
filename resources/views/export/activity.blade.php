@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

  <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
    <!-- HEADER -->
    <div class="card-header text-white py-3" style="background-color: #009FE3;"> {{-- biru PLN --}}
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0 fw-bold text-white">
            <i class="fas fa-clipboard-list me-2 text-white"></i> Report Activity
          </h5>
          <small class="text-light opacity-75">Daftar aktivitas harian dan hasil kerja lapangan</small>
        </div>

        <!-- EXPORT BUTTONS (dengan filter tanggal) -->
        <div class="d-flex gap-2">
          <a href="{{ route('export.activity.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
             class="btn btn-sm fw-semibold text-white shadow-sm"
             style="background-color: #E74C3C; border: none; transition: all 0.3s ease;">
             <i class="fas fa-file-pdf me-1 text-white"></i> PDF
          </a>

          <a href="{{ route('export.activity.csv', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
             class="btn btn-sm fw-semibold text-white shadow-sm"
             style="background-color: #27AE60; border: none; transition: all 0.3s ease;">
             <i class="fas fa-file-csv me-1 text-white"></i> CSV
          </a>

          <a href="{{ route('export.activity.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
             class="btn btn-sm fw-semibold text-white shadow-sm"
             style="background-color: #2980B9; border: none; transition: all 0.3s ease;">
             <i class="fas fa-file-excel me-1 text-white"></i> Excel
          </a>
        </div>
      </div>
    </div>

    <!-- BODY -->
    <div class="card-body" style="background-color: #F6FBFF;"> {{-- biru sangat muda --}}
      
      <!-- FILTER FORM -->
      <form method="GET" action="{{ route('activity.index') }}" class="mb-4">
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label for="start_date" class="form-label fw-semibold">Tanggal Awal</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
          </div>
          <div class="col-md-3">
            <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary fw-semibold w-100">
              <i class="fas fa-filter me-1"></i> Filter
            </button>
          </div>
          <div class="col-md-3">
            <a href="{{ route('activity.index') }}" class="btn btn-secondary fw-semibold w-100">
              <i class="fas fa-sync-alt me-1"></i> Reset
            </a>
          </div>
        </div>
      </form>

      <div class="table-responsive rounded-3 shadow-sm bg-white p-3">
        <table class="table table-hover align-middle mb-0">
          <thead class="text-white text-center" style="background-color: #009FE3;"> {{-- biru PLN --}}
            <tr>
              <th>No</th>
              <th>Sales</th>
              <th>Aktivitas</th>
              <th>Tanggal</th>
              <th>Lokasi</th>
              <th>Cluster</th>
              <th>Evidence</th>
              <th>Hasil / Kendala</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody class="text-center">
            @forelse($activities as $i => $activity)
              <tr class="hover-row">
                <td>{{ $i + 1 }}</td>
                <td class="fw-semibold text-dark">{{ $activity->sales }}</td>
                <td><span class="badge bg-primary text-white">{{ $activity->aktivitas }}</span></td>
                <td>{{ \Carbon\Carbon::parse($activity->tanggal)->format('d/m/Y') }}</td>
                <td>{{ ucfirst($activity->lokasi) }}</td>
                <td><span class="badge bg-info text-white">{{ $activity->cluster }}</span></td>
                <td>
                  @if($activity->evidence)
                    <img src="{{ asset('storage/' . $activity->evidence) }}" 
                         alt="evidence" 
                         class="img-thumbnail shadow-sm"
                         style="max-height:60px; border-radius:8px;">
                  @else
                    <span class="text-muted">No Image</span>
                  @endif
                </td>
                <td>{{ $activity->hasil_kendala ?? '-' }}</td>
                <td>
                  @if($activity->status == 'selesai')
                    <span class="badge bg-success text-white">Selesai</span>
                  @else
                    <span class="badge bg-warning text-dark">{{ ucfirst($activity->status) }}</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">
                  <i class="fas fa-database fa-3x mb-3 text-primary"></i><br>
                  Tidak ada data activity yang tersedia.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $activities->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>

<!-- STYLE TAMBAHAN -->
<style>
  .btn:hover {
      transform: translateY(-2px);
      opacity: 0.9;
  }

  .hover-row:hover {
      background-color: #E3F2FD !important; /* biru muda lembut */
      transition: 0.2s ease;
  }
</style>
@endsection
