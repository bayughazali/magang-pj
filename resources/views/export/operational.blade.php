@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

  <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
    <!-- HEADER -->
    <div class="card-header text-white py-3" style="background-color: #009FE3;">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0 fw-bold text-white">
            <i class="fas fa-clipboard-list me-2 text-white"></i> Operational Report
          </h5>
          <small class="text-light opacity-75">Data pelanggan dan operasional jaringan Icon Plus</small>
        </div>

        <!-- EXPORT BUTTONS -->
        <div class="d-flex gap-2">
          <a href="{{ route('export.operational.pdf') }}" 
             class="btn btn-sm fw-semibold text-white shadow-sm"
             style="background-color: #E74C3C; border: none; transition: all 0.3s ease;">
             <i class="fas fa-file-pdf me-1 text-white"></i> PDF
          </a>

          <a href="{{ route('export.operational.csv') }}" 
             class="btn btn-sm fw-semibold text-white shadow-sm"
             style="background-color: #27AE60; border: none; transition: all 0.3s ease;">
             <i class="fas fa-file-csv me-1 text-white"></i> CSV
          </a>

          <a href="{{ route('export.operational.excel') }}" 
             class="btn btn-sm fw-semibold text-white shadow-sm"
             style="background-color: #2980B9; border: none; transition: all 0.3s ease;">
             <i class="fas fa-file-excel me-1 text-white"></i> Excel
          </a>
        </div>
      </div>
    </div>

    <!-- BODY -->
    <div class="card-body" style="background-color: #F6FBFF;">
      <div class="table-responsive rounded-3 shadow-sm bg-white p-3">
        <table class="table table-hover align-middle mb-0">
          <thead class="text-white text-center" style="background-color: #009FE3;">
            <tr>
              <th>No</th>
              <th>ID Pelanggan</th>
              <th>Nama</th>
              <th>Bandwidth</th>
              <th>Telepon</th>
              <th>Provinsi</th>
              <th>Kabupaten</th>
              <th>Alamat</th>
              <th>Cluster</th>
              <th>Kode FAT</th>
              <th>Koordinat</th>
            </tr>
          </thead>
          <tbody class="text-center">
            @forelse($operationalData as $index => $data)
              <tr class="hover-row">
                <td>{{ $index + 1 }}</td>
                <td>{{ $data->id_pelanggan }}</td>
                <td class="fw-semibold text-primary">{{ $data->nama_pelanggan }}</td>
                <td><span class="badge bg-primary text-white">{{ $data->bandwidth }}</span></td>
                <td>{{ $data->nomor_telepon }}</td>
                <td>{{ $data->provinsi }}</td>
                <td>{{ $data->kabupaten }}</td>
                <td>{{ $data->alamat ?: '-' }}</td>
                <td>{{ $data->cluster }}</td>
                <td>{{ $data->kode_fat }}</td>
                <td>{{ $data->latitude }}, {{ $data->longitude }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center text-muted py-4">
                  <i class="fas fa-database fa-3x mb-3 text-primary"></i><br>
                  Tidak ada data operasional yang tersedia.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(isset($operationals))
      <div class="mt-3">
        {{ $operationals->links('pagination::bootstrap-5') }}
      </div>
      @endif
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
      background-color: #E3F2FD !important;
      transition: 0.2s ease;
  }
</style>
@endsection
