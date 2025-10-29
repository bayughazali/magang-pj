@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0"><i class="fas fa-users"></i> Report Competitor</h4>
    </div>
    <div class="card-body">

     <!-- FORM INPUT COMPETITOR -->
      {{-- 🔹 Ubah action form ke route competitor.store --}}
      <form id="competitorForm" action="{{ route('competitor.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label"><strong>Pilih Cluster</strong></label>
            <select class="form-control select2" name="cluster" id="clusterSelect" required>
              <option value="">-- Pilih Cluster --</option>
              {{-- 🔹 PERBAIKAN: Ambil cluster dari ReportActivity yang sudah ada data --}}
              @php
                $availableClusters = \App\Models\ReportActivity::select('cluster')
                    ->distinct()
                    ->orderBy('cluster')
                    ->pluck('cluster');
              @endphp

              @forelse($availableClusters as $cluster)
                <option value="{{ $cluster }}">Cluster {{ $cluster }}</option>
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
        </div>

        <div id="competitorInputs" style="display: none;">
        <div class="border p-3 rounded bg-light mb-3">
            <div class="row g-3 align-items-end">

            <!-- Nama Competitor -->
            <div class="col-md-6">
                <label class="form-label">Nama Competitor</label>
                <input type="text" name="competitor_name[]" class="form-control" placeholder="Ketik nama competitor..." required>
            </div>

            <!-- Paket -->
            <div class="col-md-6">
                <label class="form-label">Paket</label>
                <select name="paket[]" class="form-select" required>
                <option value="">-- Pilih Paket --</option>
                <option value="Basic">Basic</option>
                <option value="Standard">Standard</option>
                <option value="Premium">Premium</option>
                <option value="Family">Family</option>
                <option value="Business">Business</option>
                </select>
            </div>

            <!-- Kecepatan -->
            <div class="col-md-4">
                <label class="form-label">Kecepatan</label>
                <select name="kecepatan[]" class="form-select" required>
                <option value="">-- Pilih Kecepatan --</option>
                <option value="10 Mbps">10 Mbps</option>
                <option value="20 Mbps">20 Mbps</option>
                <option value="50 Mbps">50 Mbps</option>
                <option value="100 Mbps">100 Mbps</option>
                <option value="200 Mbps">200 Mbps</option>
                </select>
            </div>

            <!-- Kuota -->
            <div class="col-md-4">
                <label class="form-label">Kuota</label>
                <select name="kuota[]" class="form-select" required>
                <option value="">-- Pilih Kuota --</option>
                <option value="Unlimited">Unlimited</option>
                <option value="100 GB">100 GB</option>
                <option value="200 GB">200 GB</option>
                <option value="500 GB">500 GB</option>
                <option value="1 TB">1 TB</option>
                </select>
            </div>

            <!-- Harga -->
            <div class="col-md-4">
                <label class="form-label">Harga</label>
                <input type="number" name="harga[]" class="form-control" placeholder="Masukkan harga" required>
            </div>

            <!-- Fitur Tambahan -->
            <div class="col-md-6">
                <label class="form-label">Fitur Tambahan</label>
                <select name="fitur_tambahan[]" class="form-select">
                <option value="">-- Pilih Fitur --</option>
                <option value="Gratis Modem">Gratis Modem</option>
                <option value="Gratis TV Kabel">Gratis TV Kabel</option>
                <option value="Gratis Instalasi">Gratis Instalasi</option>
                <option value="Bebas Pemasangan">Bebas Pemasangan</option>
                <option value="Diskon 3 Bulan">Diskon 3 Bulan</option>
                </select>
            </div>

            <!-- Keterangan -->
            <div class="col-md-6">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan tambahan">
            </div>

            </div>
        </div>
        </div>
          <div id="moreCompetitors"></div>

          <button type="button" class="btn btn-outline-primary btn-sm" id="addMoreBtn">
            <i class="fas fa-plus"></i> Tambah Competitor Lain
          </button>
        </div>

        <div class="d-flex justify-content-start mt-3 col-md-10 mx-2">
          <button type="submit" class="btn btn-success px-4 py-2">
            <i class="bi bi-save me-2"></i> Simpan Data
          </button>
        </div>
      </form>

      <!-- TABEL HASIL -->
      <div class="card mt-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3">Data Competitor</h5>
          <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
              <thead class="table-dark">
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
                  {{-- Kolom Aksi hanya muncul jika admin --}}
                  @if(auth()->user()->role === 'admin')
                    <th>Aksi</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @forelse($competitors as $index => $item)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td><span class="badge bg-info">{{ $item->cluster }}</span></td>
                  <td>{{ $item->competitor_name }}</td>
                  <td>{{ $item->paket ?? '-' }}</td>
                  <td>{{ $item->kecepatan ?? '-' }}</td>
                  <td>{{ $item->kuota ?? '-' }}</td>
                  <td><strong>Rp {{ number_format($item->harga, 0, ',', '.') }}</strong></td>
                  <td>{{ $item->fitur_tambahan ?? '-' }}</td>
                  <td>{{ $item->keterangan ?? '-' }}</td>

                  {{-- Kolom Aksi: Hanya muncul jika user adalah admin --}}
                  @if(auth()->user()->role === 'admin')
                    <td>
                      {{-- Edit --}}
                      <a href="{{ route('competitor.edit', $item->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                      </a>

                      {{-- Delete --}}
                      <form action="{{ route('competitor.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data?')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  @endif
                </tr>
                @empty
                <tr>
                  <td colspan="{{ auth()->user()->role === 'admin' ? '10' : '9' }}">
                    Belum ada data competitor
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- SCRIPT -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
  $('#clusterSelect').select2({ placeholder: "Cari cluster..." });
</script>

<script>
  // Tampilkan form competitor setelah cluster dipilih
  document.getElementById("clusterSelect").addEventListener("change", function() {
    document.getElementById("competitorInputs").style.display = this.value ? "block" : "none";
  });

  // Tambah competitor lain
document.getElementById("addMoreBtn").addEventListener("click", function() {
  let div = document.createElement("div");
  div.classList.add("border", "p-3", "rounded", "bg-light", "mb-3");
  div.innerHTML = `
    <div class="row g-3 align-items-end">
      <!-- Nama Competitor -->
      <div class="col-md-6">
        <label class="form-label">Nama Competitor</label>
        <input type="text" name="competitor_name[]" class="form-control" placeholder="Ketik nama competitor..." required>
      </div>

      <!-- Paket -->
      <div class="col-md-6">
        <label class="form-label">Paket</label>
        <select name="paket[]" class="form-select" required>
          <option value="">-- Pilih Paket --</option>
          <option value="Basic">Basic</option>
          <option value="Standard">Standard</option>
          <option value="Premium">Premium</option>
          <option value="Family">Family</option>
          <option value="Business">Business</option>
        </select>
      </div>

      <!-- Kecepatan -->
      <div class="col-md-4">
        <label class="form-label">Kecepatan</label>
        <select name="kecepatan[]" class="form-select" required>
          <option value="">-- Pilih Kecepatan --</option>
          <option value="10 Mbps">10 Mbps</option>
          <option value="20 Mbps">20 Mbps</option>
          <option value="50 Mbps">50 Mbps</option>
          <option value="100 Mbps">100 Mbps</option>
          <option value="200 Mbps">200 Mbps</option>
        </select>
      </div>

      <!-- Kuota -->
      <div class="col-md-4">
        <label class="form-label">Kuota</label>
        <select name="kuota[]" class="form-select" required>
          <option value="">-- Pilih Kuota --</option>
          <option value="Unlimited">Unlimited</option>
          <option value="100 GB">100 GB</option>
          <option value="200 GB">200 GB</option>
          <option value="500 GB">500 GB</option>
          <option value="1 TB">1 TB</option>
        </select>
      </div>

      <!-- Harga -->
      <div class="col-md-4">
        <label class="form-label">Harga</label>
        <input type="number" name="harga[]" class="form-control" placeholder="Masukkan harga" required>
      </div>

      <!-- Fitur Tambahan -->
      <div class="col-md-6">
        <label class="form-label">Fitur Tambahan</label>
        <select name="fitur_tambahan[]" class="form-select">
          <option value="">-- Pilih Fitur --</option>
          <option value="Gratis Modem">Gratis Modem</option>
          <option value="Gratis TV Kabel">Gratis TV Kabel</option>
          <option value="Gratis Instalasi">Gratis Instalasi</option>
          <option value="Bebas Pemasangan">Bebas Pemasangan</option>
          <option value="Diskon 3 Bulan">Diskon 3 Bulan</option>
        </select>
      </div>

      <!-- Keterangan -->
      <div class="col-md-5">
        <label class="form-label">Keterangan</label>
        <input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan tambahan">
      </div>

      <!-- Tombol Hapus -->
      <div class="col-md-1">
        <button type="button" class="btn btn-danger btn-sm removeRow">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
  `;
  document.getElementById("moreCompetitors").appendChild(div);

  // tombol hapus
  div.querySelector(".removeRow").addEventListener("click", function() {
    div.remove();
  });
});
</script>

<style>
  /* Style dropdown modern */
  select.form-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: #fff url("data:image/svg+xml;utf8,<svg fill='gray' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>") no-repeat right 12px center;
    background-size: 16px;
    border: 1px solid #ced4da;
    border-radius: 10px;
    padding: 10px 40px 10px 15px;
    font-size: 14px;
    color: #333;
    width: 100%;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
  }

  select.form-select:hover {
    border-color: #5e72e4;
    box-shadow: 0 0 6px rgba(94, 114, 228, 0.3);
  }

  select.form-select:focus {
    outline: none;
    border-color: #324cdd;
    box-shadow: 0 0 6px rgba(50, 76, 221, 0.5);
  }
</style>

@endsection
