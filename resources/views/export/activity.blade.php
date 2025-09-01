@extends('layouts.app')

@section('content')

<!-- Header Card -->
<div class="header bg-gradient-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Export Report Activity</h6>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <a href="{{ route('export.activity.pdf') }}" class="btn btn-sm btn-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
          </a>
          <a href="{{ route('export.activity.csv') }}" class="btn btn-sm btn-success">
            <i class="fas fa-file-csv"></i> Export CSV
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Page Content -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col-xl-12">
      <div class="card shadow">
        <!-- Card header -->
        <div class="card-header bg-gradient-info border-0 d-flex align-items-center justify-content-between">
          <h3 class="mb-0 text-white">
            <i class="fas fa-clipboard-list mr-2"></i> Daftar Report Activity
          </h3>
        </div>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table align-items-center table-flush table-hover">
            <thead class="thead-dark text-center">
              <tr>
                <th scope="col">No</th>
                <th scope="col">Nama</th>
                <th scope="col">Kegiatan</th>
                <th scope="col">Tanggal</th>
                <th scope="col">Lokasi</th>
                <th scope="col">Cluster</th>
                <th scope="col">Evidence</th>
              </tr>
            </thead>
            <tbody class="text-center">
              @forelse($activities as $i => $activity)
              <tr>
                <td>{{ $i+1 }}</td>
                <td><strong>{{ $activity->sales }}</strong></td>
                <td>
                  <span class="badge badge-pill badge-primary">
                    {{ $activity->aktivitas }}
                  </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($activity->tanggal)->format('d/m/Y') }}</td>
                <td>{{ ucfirst($activity->lokasi) }}</td>
                <td>
                  <span class="badge badge-pill badge-info">{{ $activity->cluster }}</span>
                </td>
                <td>
                  @if($activity->evidence)
                  <img src="{{ asset('storage/'.$activity->evidence) }}"
                       alt="evidence"
                       class="img-thumbnail shadow-sm"
                       style="max-height:60px; border-radius:8px">
                  @else
                  <span class="text-muted">No Image</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted">Tidak ada data activity</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Card footer -->
        <div class="card-footer py-4">
          {{ $activities->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
