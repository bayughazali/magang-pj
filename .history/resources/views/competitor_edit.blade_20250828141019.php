@extends('layouts.app')

@section('content')
<div class="container mt-4">
  <div class="card shadow">
    <div class="card-header bg-primary">
      <h4 class="mb-0 text-white"><i class="fas fa-edit"></i> Edit Competitor</h4>
    </div>
    <div class="card-body">
      <form action="{{ route('competitor.update', $competitor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Cluster -->
        <div class="mb-3">
          <label class="form-label"><strong>Pilih Cluster</strong></label>
          <select name="cluster" class="form-control select2" required>
            <option value="">-- Pilih Cluster --</option>
            {{-- 🔹 PERBAIKAN: Ambil cluster dari ReportActivity yang sudah ada data --}}
            @php
              $availableClusters = \App\Models\ReportActivity::select('cluster')
                  ->distinct()
                  ->orderBy('cluster')
                  ->pluck('cluster');
            @endphp

            @forelse($availableClusters as $cluster)
              <option value="{{ $cluster }}" {{ $competitor->cluster == $cluster ? 'selected' : '' }}>
                Cluster {{ $cluster }}
              </option>
            @empty
              <option disabled>Belum ada data Report Activity</option>
            @endforelse
          </select>

          @if($availableClusters->isEmpty())
            <small class="text-muted">
              <i class="fas fa-info-circle"></i>
              Cluster akan muncul setelah ada data Report Activity
            </small>
          @endif
        </div>

        <div class="border p-3 rounded bg-light mb-3">
          <div class="row g-3">

            <!-- Nama Competitor -->
            <div class="col-md-6">
              <label class="form-label">Nama Competitor</label>
              <input type="text" name="competitor_name" class="form-control" value="{{ $competitor->competitor_name }}" placeholder="Ketik nama competitor..." required>
            </div>

            <!-- Paket -->
            <div class="col-md-6">
              <label class="form-label">Paket</label>
              <select name="paket" class="form-select" required>
                <option value="">-- Pilih Paket --</option>
                <option value="Basic" {{ $competitor->paket == 'Basic' ? 'selected' : '' }}>Basic</option>
                <option value="Standard" {{ $competitor->paket == 'Standard' ? 'selected' : '' }}>Standard</option>
                <option value="Premium" {{ $competitor->paket == 'Premium' ? 'selected' : '' }}>Premium</option>
                <option value="Family" {{ $competitor->paket == 'Family' ? 'selected' : '' }}>Family</option>
                <option value="Business" {{ $competitor->paket == 'Business' ? 'selected' : '' }}>Business</option>
              </select>
            </div>

            <!-- Kecepatan -->
            <div class="col-md-4">
              <label class="form-label">Kecepatan</label>
              <select name="kecepatan" class="form-select" required>
                <option value="">-- Pilih Kecepatan --</option>
                <option value="10 Mbps" {{ $competitor->kecepatan == '10 Mbps' ? 'selected' : '' }}>10 Mbps</option>
                <option value="20 Mbps" {{ $competitor->kecepatan == '20 Mbps' ? 'selected' : '' }}>20 Mbps</option>
                <option value="50 Mbps" {{ $competitor->kecepatan == '50 Mbps' ? 'selected' : '' }}>50 Mbps</option>
                <option value="100 Mbps" {{ $competitor->kecepatan == '100 Mbps' ? 'selected' : '' }}>100 Mbps</option>
                <option value="200 Mbps" {{ $competitor->kecepatan == '200 Mbps' ? 'selected' : '' }}>200 Mbps</option>
              </select>
            </div>

            <!-- Kuota -->
            <div class="col-md-4">
              <label class="form-label">Kuota</label>
              <select name="kuota" class="form-select" required>
                <option value="">-- Pilih Kuota --</option>
                <option value="Unlimited" {{ $competitor->kuota == 'Unlimited' ? 'selected' : '' }}>Unlimited</option>
                <option value="100 GB" {{ $competitor->kuota == '100 GB' ? 'selected' : '' }}>100 GB</option>
                <option value="200 GB" {{ $competitor->kuota == '200 GB' ? 'selected' : '' }}>200 GB</option>
                <option value="500 GB" {{ $competitor->kuota == '500 GB' ? 'selected' : '' }}>500 GB</option>
                <option value="1 TB" {{ $competitor->kuota == '1 TB' ? 'selected' : '' }}>1 TB</option>
              </select>
            </div>

            <!-- Harga -->
            <div class="col-md-4">
              <label class="form-label">Harga</label>
              <input type="number" name="harga" class="form-control" value="{{ $competitor->harga }}" placeholder="Masukkan harga" required>
            </div>

            <!-- Fitur Tambahan -->
            <div class="col-md-6">
              <label class="form-label">Fitur Tambahan</label>
              <select name="fitur_tambahan" class="form-select">
                <option value="">-- Pilih Fitur --</option>
                <option value="Gratis Modem" {{ $competitor->fitur_tambahan == 'Gratis Modem' ? 'selected' : '' }}>Gratis Modem</option>
                <option value="Gratis TV Kabel" {{ $competitor->fitur_tambahan == 'Gratis TV Kabel' ? 'selected' : '' }}>Gratis TV Kabel</option>
                <option value="Gratis Instalasi" {{ $competitor->fitur_tambahan == 'Gratis Instalasi' ? 'selected' : '' }}>Gratis Instalasi</option>
                <option value="Bebas Pemasangan" {{ $competitor->fitur_tambahan == 'Bebas Pemasangan' ? 'selected' : '' }}>Bebas Pemasangan</option>
                <option value="Diskon 3 Bulan" {{ $competitor->fitur_tambahan == 'Diskon 3 Bulan' ? 'selected' : '' }}>Diskon 3 Bulan</option>
              </select>
            </div>

            <!-- Keterangan -->
            <div class="col-md-6">
              <label class="form-label">Keterangan</label>
              <input type="text" name="keterangan" class="form-control" value="{{ $competitor->keterangan }}" placeholder="Keterangan tambahan">
            </div>

          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Update Data
          </button>
          <a href="{{ route('competitor.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Batal
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Scripts untuk Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('.select2').select2({
      placeholder: "Pilih cluster...",
      width: '100%'
    });
  });
</script>
@endsection
