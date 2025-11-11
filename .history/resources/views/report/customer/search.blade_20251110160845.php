@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">

    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="fw-bold mb-1 text-primary">
                    <i class="fas fa-users me-2"></i> Cari Pelanggan & Kode FAT
                </h4>
                <p class="text-muted mb-0">Pencarian data pelanggan berdasarkan berbagai kriteria</p>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error') || $errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') ?: $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('customer.search') }}" class="row g-3 align-items-end">
                <!-- Search Input -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search me-1"></i> Kata Kunci
                    </label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control shadow-sm"
                           placeholder="ID, Nama, Telepon, Kode FAT...">
                </div>

                <!-- Provinsi Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-map-marked-alt me-1"></i> Provinsi
                    </label>
                    <select name="provinsi" id="provinsi_filter" class="form-select shadow-sm">
                        <option value="">Semua Provinsi</option>
                        @foreach($regionData as $provinsi => $kabupatenList)
                            <option value="{{ $provinsi }}" {{ request('provinsi') == $provinsi ? 'selected' : '' }}>
                                {{ strtoupper($provinsi) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kabupaten Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-map me-1"></i> Kabupaten
                    </label>
                    <select name="kabupaten" id="kabupaten_filter" class="form-select shadow-sm">
                        <option value="">Semua Kabupaten</option>
                    </select>
                </div>

                <!-- Kecamatan Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-map-pin me-1"></i> Kecamatan
                    </label>
                    <select name="kecamatan" id="kecamatan_filter" class="form-select shadow-sm">
                        <option value="">Semua Kecamatan</option>
                    </select>
                </div>

                <!-- Bandwidth Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-wifi me-1"></i> Bandwidth
                    </label>
                    <select name="bandwidth" class="form-select shadow-sm">
                        <option value="">Semua Bandwidth</option>
                        <option value="10 Mbps" {{ request('bandwidth') == '10 Mbps' ? 'selected' : '' }}>10 Mbps</option>
                        <option value="20 Mbps" {{ request('bandwidth') == '20 Mbps' ? 'selected' : '' }}>20 Mbps</option>
                        <option value="50 Mbps" {{ request('bandwidth') == '50 Mbps' ? 'selected' : '' }}>50 Mbps</option>
                        <option value="100 Mbps" {{ request('bandwidth') == '100 Mbps' ? 'selected' : '' }}>100 Mbps</option>
                    </select>
                </div>

                <!-- Submit Buttons -->
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Reset Button -->
            <div class="row mt-3">
                <div class="col-12">
                    <a href="{{ route('customer.search') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo me-1"></i> Reset Filter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-success shadow-sm" role="alert">
        <strong><i class="fas fa-database"></i> Data Pelanggan Tersimpan</strong><br>
        <span>Daftar pelanggan yang telah diinput ke sistem</span>
    </div>

    <!-- Results Table -->
    @if(isset($pelanggans) && !empty($pelanggans) && (is_countable($pelanggans) ? count($pelanggans) > 0 : (method_exists($pelanggans, 'count') ? $pelanggans->count() > 0 : false)))
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-table me-2"></i>Hasil Pencarian
                        <span class="badge bg-primary ms-2">
                            {{ method_exists($pelanggans, 'total') ? $pelanggans->total() : (is_countable($pelanggans) ? count($pelanggans) : 0) }}
                        </span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">ID Pelanggan</th>
                                    <th width="15%">Nama</th>
                                    <th width="8%">Bandwidth</th>
                                    <th width="20%">Alamat</th>
                                    <th width="10%">Telepon</th>
                                    <th width="8%">Provinsi</th>
                                    <th width="8%">Kabupaten</th>
                                    <th width="8%">Kecamatan</th>
                                    <th width="8%">Kode FAT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $startIndex = method_exists($pelanggans, 'firstItem') ? $pelanggans->firstItem() : 1;
                                @endphp

                                @foreach($pelanggans as $index => $pelanggan)
                                <tr id="row-{{ $pelanggan->id }}">
                                    <td>{{ $startIndex + $index }}</td>
                                    <td>
                                        <span class="fw-semibold text-primary">{{ $pelanggan->id_pelanggan ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $pelanggan->nama_pelanggan ?? 'N/A' }}</div>
                                        <small class="text-muted">
                                            {{ isset($pelanggan->created_at) ? \Carbon\Carbon::parse($pelanggan->created_at)->format('d M Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $pelanggan->bandwidth ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ isset($pelanggan->alamat) ? Str::limit($pelanggan->alamat, 30) : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $pelanggan->nomor_telepon ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $pelanggan->provinsi ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $pelanggans->kabupaten ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $pelanggans->kecamatan ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if(isset($pelanggans->kode_fat) && $pelanggans->kode_fat)
                                            <span class="badge bg-warning text-dark">{{ $pelanggans->kode_fat }}</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Ada</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(method_exists($pelanggans, 'appends'))
                <div class="card-footer bg-light">
                    {{ $pelanggans->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada data yang ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarian Anda</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Info Count -->
@if(isset($pelanggans) && !empty($pelanggans) && (is_countable($pelanggans) ? count($pelanggans) > 0 : (method_exists($pelanggans, 'count') ? $pelanggans->count() > 0 : false)))
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-end align-items-center">
                    <small class="text-muted">
                        @if(method_exists($pelanggans, 'firstItem'))
                            Menampilkan {{ $pelanggans->firstItem() }}-{{ $pelanggans->lastItem() }} dari {{ $pelanggans->total() }} data
                        @else
                            Menampilkan {{ count($pelanggans) }} data
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.form-label {
    font-size: 0.85rem;
    margin-bottom: 0.3rem;
}

.form-select, .form-control {
    font-size: 0.9rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinsiFilter = document.getElementById('provinsi_filter');
    const kabupatenFilter = document.getElementById('kabupaten_filter');
    const kecamatanFilter = document.getElementById('kecamatan_filter');

    // Data untuk pre-populate dari request
    const oldProvinsi = '{{ request("provinsi") }}';
    const oldKabupaten = '{{ request("kabupaten") }}';
    const oldKecamatan = '{{ request("kecamatan") }}';

    console.log('Initial filter values:', {
        provinsi: oldProvinsi,
        kabupaten: oldKabupaten,
        kecamatan: oldKecamatan
    });

    // Event: Provinsi berubah → Load Kabupaten
    provinsiFilter.addEventListener('change', function() {
        const provinsi = this.value;
        console.log('Provinsi selected:', provinsi);

        // Reset kabupaten dan kecamatan
        kabupatenFilter.innerHTML = '<option value="">Semua Kabupaten</option>';
        kabupatenFilter.disabled = true;
        kecamatanFilter.innerHTML = '<option value="">Semua Kecamatan</option>';
        kecamatanFilter.disabled = true;

        if (provinsi) {
            const url = `/report/operational/get-kabupaten?provinsi=${encodeURIComponent(provinsi)}`;
            console.log('Fetching kabupaten:', url);

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Kabupaten response:', data);

                    if (data.kabupaten && Array.isArray(data.kabupaten) && data.kabupaten.length > 0) {
                        kabupatenFilter.innerHTML = '<option value="">Semua Kabupaten</option>';
                        data.kabupaten.forEach(kab => {
                            const option = document.createElement('option');
                            option.value = kab;
                            option.textContent = kab;
                            kabupatenFilter.appendChild(option);
                        });
                        kabupatenFilter.disabled = false;

                        // Restore old kabupaten if exists
                        if (oldKabupaten && provinsi === oldProvinsi) {
                            kabupatenFilter.value = oldKabupaten;
                            kabupatenFilter.dispatchEvent(new Event('change'));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading kabupaten:', error);
                });
        }
    });

    // Event: Kabupaten berubah → Load Kecamatan
    kabupatenFilter.addEventListener('change', function() {
        const provinsi = provinsiFilter.value;
        const kabupaten = this.value;
        console.log('Kabupaten selected:', kabupaten);

        // Reset kecamatan
        kecamatanFilter.innerHTML = '<option value="">Semua Kecamatan</option>';
        kecamatanFilter.disabled = true;

        if (provinsi && kabupaten) {
            const url = `/report/operational/get-kecamatan?provinsi=${encodeURIComponent(provinsi)}&kabupaten=${encodeURIComponent(kabupaten)}`;
            console.log('Fetching kecamatan:', url);

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Kecamatan response:', data);

                    if (data.success && data.kecamatan && Array.isArray(data.kecamatan) && data.kecamatan.length > 0) {
                        kecamatanFilter.innerHTML = '<option value="">Semua Kecamatan</option>';
                        data.kecamatan.forEach(kec => {
                            const option = document.createElement('option');
                            option.value = kec;
                            option.textContent = kec;
                            kecamatanFilter.appendChild(option);
                        });
                        kecamatanFilter.disabled = false;

                        // Restore old kecamatan if exists
                        if (oldKecamatan && kabupaten === oldKabupaten) {
                            kecamatanFilter.value = oldKecamatan;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading kecamatan:', error);
                });
        }
    });

    // Initialize: Trigger cascade if provinsi has value
    if (oldProvinsi) {
        console.log('Initializing with provinsi:', oldProvinsi);
        provinsiFilter.dispatchEvent(new Event('change'));
    }
});
</script>

@endsection
