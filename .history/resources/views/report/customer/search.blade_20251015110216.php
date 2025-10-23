@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-search fa-2x me-3"></i>
                            <div>
                                <h4 class="mb-1">Cari Pelanggan & Kode FAT</h4>
                                <p class="mb-0 opacity-75">Pencarian data pelanggan berdasarkan berbagai kriteria</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0">{{ isset($pelanggans) && !empty($pelanggans) ? (is_countable($pelanggans) ? count($pelanggans) : (method_exists($pelanggans, 'total') ? $pelanggans->total() : 0)) : 0 }}</h5>
                            <small class="opacity-75">Total Data</small>
                        </div>
                    </div>
                </div>
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

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filter Pencarian
                        </h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary active" onclick="showBasicSearch()">
                                <i class="fas fa-search me-1"></i>Basic
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="showAdvancedSearch()">
                                <i class="fas fa-sliders-h me-1"></i>Advanced
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Basic Search Form -->
                    <div id="basicSearchForm">
                        <form method="GET" action="{{ route('customer.search') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="filter_field" class="form-label fw-semibold">Filter Field</label>
                                    <select class="form-select" id="filter_field" name="filter_field">
                                        <option value="">Semua Field</option>
                                        <option value="id_pelanggan" {{ request('filter_field') == 'id_pelanggan' ? 'selected' : '' }}>ID Pelanggan</option>
                                        <option value="nama_pelanggan" {{ request('filter_field') == 'nama_pelanggan' ? 'selected' : '' }}>Nama Pelanggan</option>
                                        <option value="bandwidth" {{ request('filter_field') == 'bandwidth' ? 'selected' : '' }}>Bandwidth</option>
                                        <option value="alamat" {{ request('filter_field') == 'alamat' ? 'selected' : '' }}>Alamat</option>
                                        <option value="provinsi" {{ request('filter_field') == 'provinsi' ? 'selected' : '' }}>Provinsi</option>
                                        <option value="kabupaten" {{ request('filter_field') == 'kabupaten' ? 'selected' : '' }}>Kabupaten</option>
                                        <option value="nomor_telepon" {{ request('filter_field') == 'nomor_telepon' ? 'selected' : '' }}>Nomor Telepon</option>
                                        <option value="cluster" {{ request('filter_field') == 'cluster' ? 'selected' : '' }}>Cluster</option>
                                        <option value="kode_fat" {{ request('filter_field') == 'kode_fat' ? 'selected' : '' }}>Kode FAT</option>
                                        <option value="latitude" {{ request('filter_field') == 'latitude' ? 'selected' : '' }}>Latitude</option>
                                        <option value="longitude" {{ request('filter_field') == 'longitude' ? 'selected' : '' }}>Longitude</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="filter_query" class="form-label fw-semibold">Kata Kunci</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" id="filter_query" name="filter_query"
                                               placeholder="Masukkan kata kunci pencarian..."
                                               value="{{ request('filter_query') ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="btn-group w-100">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>Cari
                                        </button>
                                        <a href="{{ route('customer.search') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Advanced Search Form (Hidden by default) -->
                    <div id="advancedSearchForm" style="display: none;">
                        <form method="GET" action="{{ route('customer.search') }}">
                            <input type="hidden" name="advanced" value="1">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cluster</label>
                                    <select class="form-select" name="cluster_filter">
                                        <option value="">Semua Cluster</option>
                                        @if(isset($clusters) && !empty($clusters))
                                            @foreach($clusters as $cluster)
                                                <option value="{{ $cluster }}" {{ request('cluster_filter') == $cluster ? 'selected' : '' }}>{{ $cluster }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Provinsi</label>
                                    <select class="form-select" name="provinsi_filter">
                                        <option value="">Semua Provinsi</option>
                                        @if(isset($provinsis) && !empty($provinsis))
                                            @foreach($provinsis as $provinsi)
                                                <option value="{{ $provinsi }}" {{ request('provinsi_filter') == $provinsi ? 'selected' : '' }}>{{ $provinsi }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Kabupaten</label>
                                    <select class="form-select" name="kabupaten_filter">
                                        <option value="">Semua Kabupaten</option>
                                        @if(isset($kabupatens) && !empty($kabupatens))
                                            @foreach($kabupatens as $kabupaten)
                                                <option value="{{ $kabupaten }}" {{ request('kabupaten_filter') == $kabupaten ? 'selected' : '' }}>{{ $kabupaten }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Range Bandwidth</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="bandwidth_min" placeholder="Min" value="{{ request('bandwidth_min') }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="bandwidth_max" placeholder="Max" value="{{ request('bandwidth_max') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tanggal Registrasi</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Kode FAT</label>
                                    <select class="form-select" name="has_fat_code">
                                        <option value="">Semua</option>
                                        <option value="yes" {{ request('has_fat_code') == 'yes' ? 'selected' : '' }}>Ada FAT</option>
                                        <option value="no" {{ request('has_fat_code') == 'no' ? 'selected' : '' }}>Tidak Ada FAT</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Koordinat</label>
                                    <select class="form-select" name="has_coordinates">
                                        <option value="">Semua</option>
                                        <option value="yes" {{ request('has_coordinates') == 'yes' ? 'selected' : '' }}>Ada Koordinat</option>
                                        <option value="no" {{ request('has_coordinates') == 'no' ? 'selected' : '' }}>Tidak Ada Koordinat</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-filter me-1"></i>Advanced Search
                                        </button>
                                        <a href="{{ route('customer.search') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Buttons -->
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
                                    <th width="12%">ID Pelanggan</th>
                                    <th width="18%">Nama</th>
                                    <th width="8%">Bandwidth</th>
                                    <th width="25%">Alamat</th>
                                    <th width="12%">Telepon</th>
                                    <th width="10%">Cluster</th>
                                    <th width="10%">Kode FAT</th>
                                    <th width="5%">Aksi</th>
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
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $pelanggan->nama_pelanggan ?? 'N/A' }}</div>
                                            <small class="text-muted">
                                                {{ isset($pelanggan->created_at) ? \Carbon\Carbon::parse($pelanggan->created_at)->format('d M Y') : 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $pelanggan->bandwidth ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ isset($pelanggan->alamat) ? Str::limit($pelanggan->alamat, 40) : 'N/A' }}</small>
                                    @if(isset($pelanggan->provinsi) && $pelanggan->provinsi)
                                        <br><small class="text-info">{{ $pelanggan->provinsi }}, {{ $pelanggan->kabupaten ?? '' }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $pelanggan->nomor_telepon ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $pelanggan->cluster ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if(isset($pelanggan->kode_fat) && $pelanggan->kode_fat)
                                        <span class="badge bg-success">{{ $pelanggan->kode_fat }}</span>
                                    @else
                                        <span class="badge bg-warning">Tidak Ada</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button"
                                        class="btn btn-outline-danger btn-delete"
                                        data-id="{{ $pelanggan->id }}"
                                        data-name="{{ $pelanggan->nama_pelanggan ?? 'N/A' }}"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                    @if(request()->hasAny(['filter_query', 'filter_field', 'advanced']))
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data yang ditemukan</h5>
                        <p class="text-muted">Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                        <a href="{{ route('customer.search') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-1"></i>Cari Lagi
                        </a>
                    @else
                        <i class="fas fa-search fa-3x text-primary mb-3"></i>
                        <h5>Pencarian Pelanggan & Kode FAT</h5>
                        <p class="text-muted">Gunakan form pencarian di atas untuk mencari data pelanggan</p>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary" onclick="showAllData()">
                                <i class="fas fa-list me-1"></i>Lihat Semua Data
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="showAdvancedSearch()">
                                <i class="fas fa-sliders-h me-1"></i>Advanced Search
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Konfirmasi Delete -->
<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">
          <i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Hapus
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data pelanggan berikut?</p>
        <div class="alert alert-warning">
          <strong id="customerName">-</strong>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">
          <i class="fas fa-trash me-1"></i> Hapus
        </button>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlDeleteBase = '{{ url("/customer") }}'; // /customer
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let currentDeleteId = null;

    // Event delegation: tangani klik tombol delete
    document.querySelector('table').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete');
        if (!btn) return;

        currentDeleteId = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-name') || 'N/A';
        document.getElementById('customerName').textContent = nama;

        // tampilkan modal bootstrap
        const modalEl = document.getElementById('deleteModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // Konfirmasi modal -> lakukan delete
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (!currentDeleteId) return;

        const deleteBtn = this;
        const originalHtml = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
        deleteBtn.disabled = true;

        fetch(`${urlDeleteBase}/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(async res => {
            let body = null;
            try { body = await res.json(); } catch(e) {}
            if (res.ok) {
                // tutup modal
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modalInstance) modalInstance.hide();

                // hapus baris tabel
                const row = document.getElementById('row-' + currentDeleteId);
                if (row) row.remove();

                // notifikasi sederhana
                alert(body?.message || 'Data berhasil dihapus');
                // update count / reload kalau perlu
                setTimeout(() => location.reload(), 700);
            } else {
                alert(body?.message || `Gagal menghapus (status ${res.status})`);
            }
        })
        .catch(err => {
            console.error('Delete error:', err);
            alert('Terjadi kesalahan koneksi.');
        })
        .finally(() => {
            deleteBtn.innerHTML = originalHtml;
            deleteBtn.disabled = false;
            currentDeleteId = null;
        });
    });
});
</script>

<script>
 const urlDeleteBase = '{{ url("/customer") }}';
    let currentDeleteId = null;

    window.deletePelanggan = function(id, nama) {
        currentDeleteId = id;
        document.getElementById('customerName').textContent = nama || 'N/A';
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (!currentDeleteId) return;
        fetch(`${urlDeleteBase}/${currentDeleteId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => location.reload());
    });
</script>



@endsection

@push('scripts')

<script>
function deletePelanggan(id, nama) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus data pelanggan berikut?<br><strong>${nama}</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/report/operational/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                Swal.fire('Berhasil', data.message || 'Data pelanggan dihapus!', 'success')
                    .then(() => location.reload());
            })
            .catch(error => {
                Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
            });
        }
    });
}
</script>
<script>
function deletePelanggan(id, nama) {
    if (!confirm(`Apakah kamu yakin ingin menghapus pelanggan "${nama}"?`)) return;

    fetch(`/report/customer/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // refresh halaman setelah hapus berhasil
        } else {
            alert(data.message || 'Gagal menghapus data.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan server.');
    });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Base URL delete
    const urlDeleteBase = '{{ url("/customer") }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let currentDeleteId = null;

    // Fungsi dipanggil dari tombol hapus di tabel
    window.deletePelanggan = function(id, nama) {
    console.log('ID dikirim ke backend:', id, 'Nama:', nama);
    if (!confirm(`Yakin ingin menghapus data pelanggan ${nama}?`)) return;

    fetch(`/customer/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    })
    .catch(err => console.error(err));
}

    // Ketika tombol hapus di modal ditekan
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    confirmDeleteBtn.addEventListener('click', function() {
        if (!currentDeleteId) return;

        // Ubah tombol jadi loading
        const originalHtml = confirmDeleteBtn.innerHTML;
        confirmDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
        confirmDeleteBtn.disabled = true;

        fetch(`${urlDeleteBase}/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(async res => {
            let body = null;
            try { body = await res.json(); } catch(e) {}

            if (res.ok) {
                // Tutup modal
                const modalEl = document.getElementById('deleteModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // Hapus baris langsung dari tabel
                const row = document.getElementById('row-' + currentDeleteId);
                if (row) row.remove();

                // Tampilkan notifikasi sukses
                showAlert(body?.message || 'Data berhasil dihapus', 'success');

                // Jika semua baris habis, reload halaman
                if (document.querySelectorAll('tbody tr').length === 0) {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                showAlert(body?.message || `Gagal menghapus (status ${res.status})`, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('Terjadi kesalahan koneksi.', 'error');
        })
        .finally(() => {
            confirmDeleteBtn.innerHTML = originalHtml;
            confirmDeleteBtn.disabled = false;
            currentDeleteId = null;
        });
    });

    // Fungsi alert bootstrap dinamis
    function showAlert(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 4000);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // base URL untuk delete — sesuaikan kalau route-mu berbeda
    const urlDeleteBase = '{{ url("/customer") }}'; // hasil: /customer

    let currentDeleteId = null;

    // fungsi dipanggil dari tombol yang kamu pakai:
    window.deletePelanggan = function(id, nama) {
        currentDeleteId = id;
        document.getElementById('customerName').textContent = nama || 'N/A';
        const modalEl = document.getElementById('deleteModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    };

    // tombol konfirmasi di modal
    const confirmBtn = document.getElementById('confirmDelete');
    confirmBtn.addEventListener('click', function() {
        if (!currentDeleteId) return;

        // disable dan beri loading feedback
        const originalHtml = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
        confirmBtn.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            alert('CSRF token tidak ditemukan. Refresh halaman dan coba lagi.');
            resetBtn();
            return;
        }

        const deleteUrl = `${urlDeleteBase}/${currentDeleteId}`; // ex: /customer/123

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })
        .then(async (res) => {
            // ambil body (jika JSON)
            let body = null;
            try { body = await res.json(); } catch(e) { body = null; }

            if (res.ok) {
                // sukses
                // tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) modal.hide();

                // optional: hapus baris dari tabel tanpa reload
                const row = document.getElementById('row-' + currentDeleteId);
                if (row) row.remove();

                // tampilkan notifikasi singkat
                alert((body && body.message) ? body.message : 'Data berhasil dihapus');

                // kalau kamu pakai pagination / ingin update count, reload kecil:
                setTimeout(() => location.reload(), 700);
            } else {
                // gagal (404, 419, 500, dll)
                let message = (body && (body.message || body.error)) ? (body.message || body.error) : `Gagal menghapus (status ${res.status})`;
                alert(message);
            }
        })
        .catch(err => {
            console.error('Delete error:', err);
            alert('Terjadi kesalahan koneksi. Cek console untuk detail.');
        })
        .finally(() => {
            resetBtn();
            currentDeleteId = null;
        });

        function resetBtn() {
            confirmBtn.innerHTML = originalHtml;
            confirmBtn.disabled = false;
        }
    });
});

    let deleteId = null;

  // ketika tombol hapus di tabel diklik
  document.querySelectorAll('.deleteButton').forEach(button => {
    button.addEventListener('click', function () {
      deleteId = this.getAttribute('data-id'); // ambil id pelanggan
    });
  });

  // ketika konfirmasi hapus diklik di modal
  document.getElementById('confirmDelete').addEventListener('click', function () {
    if (deleteId) {
      fetch(`/customers/${deleteId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
      })
      .then(response => {
        if (response.ok) {
          // sukses hapus → reload halaman
          location.reload();
        } else {
          alert('Gagal menghapus data');
        }
      })
      .catch(error => {
        console.error(error);
        alert('Terjadi kesalahan');
      });
    }
  });

document.addEventListener('DOMContentLoaded', function () {
    let deleteId = null; // simpan id customer yang mau dihapus

    // Ketika tombol hapus di tabel diklik → ambil ID-nya
    const deleteButtons = document.querySelectorAll('[data-bs-target="#deleteModal"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            deleteId = this.getAttribute('data-id');
        });
    });

    // Ketika tombol "Hapus" di modal ditekan
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (!deleteId) return;

        fetch(`/customer/${deleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Gagal menghapus data');
            return response.json();
        })
        .then(data => {
            // Tutup modal dan refresh data
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            modal.hide();
            alert(data.message || 'Data berhasil dihapus');
            location.reload();
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        });
    });
});

fetch(`/customer/${id}`, {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
    }
})

// Global variables
let currentDeleteId = null;

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto focus search input
    const searchInput = document.getElementById('filter_query');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }

    // Pastikan ada CSRF token di meta tag
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const metaTag = document.createElement('meta');
        metaTag.name = 'csrf-token';
        metaTag.content = '{{ csrf_token() }}';
        document.head.appendChild(metaTag);
    }

    // Setup delete confirmation modal
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (currentDeleteId) {
                performDelete(currentDeleteId);
                // Close modal
                const deleteModalEl = document.getElementById('deleteModal');
                const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
                if (deleteModal) {
                    deleteModal.hide();
                }
            }
        });
    }

    // Check if advanced search should be shown
    const hasAdvancedParams = {{ request()->has('advanced') || request()->has('cluster_filter') || request()->has('provinsi_filter') ? 'true' : 'false' }};
    if (hasAdvancedParams) {
        showAdvancedSearch();
    }
});

function showBasicSearch() {
    document.getElementById('basicSearchForm').style.display = 'block';
    document.getElementById('advancedSearchForm').style.display = 'none';

    // Update button states
    const buttons = document.querySelectorAll('.btn-group-sm .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function showAdvancedSearch() {
    document.getElementById('basicSearchForm').style.display = 'none';
    document.getElementById('advancedSearchForm').style.display = 'block';

    // Update button states
    const buttons = document.querySelectorAll('.btn-group-sm .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    if (event && event.target) {
        event.target.classList.add('active');
    } else {
        // If called programmatically, find and activate the advanced button
        const advancedBtn = document.querySelector('.btn-group-sm .btn-outline-info');
        if (advancedBtn) {
            advancedBtn.classList.add('active');
        }
    }
}

function deletePelanggan(id, nama) {
    currentDeleteId = id;
    document.getElementById('customerName').textContent = nama;

    // Show modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function performDelete(id) {
    // Show loading state
    const deleteBtn = document.getElementById('confirmDelete');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
    deleteBtn.disabled = true;

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        showAlert('CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.', 'error');
        resetDeleteButton(deleteBtn, originalText);
        return;
    }

    // Use the correct delete URL format
    const deleteUrl = `/customer/${id}`;

    // Send delete request via fetch
    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Delete response:', data);

        if (data.success) {
            showAlert(data.message || 'Data pelanggan berhasil dihapus!', 'success');
            // Remove row from table instead of full reload for better UX
            const row = document.getElementById('row-' + id);
            if (row) {
                row.remove();
                updateRowNumbers();
                updateTotalCount();
            }
        } else {
            throw new Error(data.message || 'Gagal menghapus data');
        }
        resetDeleteButton(deleteBtn, originalText);
        currentDeleteId = null;
    })
    .catch(error => {
        console.error('Error deleting customer:', error);
        showAlert(`Gagal menghapus data pelanggan: ${error.message}`, 'error');
        resetDeleteButton(deleteBtn, originalText);
    });
}

function resetDeleteButton(btn, originalText) {
    btn.innerHTML = originalText;
    btn.disabled = false;
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        const firstCell = row.querySelector('td');
        if (firstCell) {
            const currentPage = {{ isset($pelanggans) && method_exists($pelanggans, 'currentPage') ? $pelanggans->currentPage() : 1 }};
            const perPage = {{ isset($pelanggans) && method_exists($pelanggans, 'perPage') ? $pelanggans->perPage() : 10 }};
            const newNumber = ((currentPage - 1) * perPage) + index + 1;
            firstCell.textContent = newNumber;
        }
    });
}

function updateTotalCount() {
    const remainingRows = document.querySelectorAll('tbody tr').length;
    const totalBadge = document.querySelector('.badge.bg-primary');
    if (totalBadge) {
        const currentTotal = parseInt(totalBadge.textContent) - 1;
        totalBadge.textContent = currentTotal;
    }

    // Update header total
    const headerTotal = document.querySelector('.text-end h5');
    if (headerTotal) {
        const currentHeaderTotal = parseInt(headerTotal.textContent) - 1;
        headerTotal.textContent = currentHeaderTotal;
    }

    // If no more rows, show empty state
    if (remainingRows === 0) {
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
}

function showAlert(message, type = 'success') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const alertHtml = `
        <div class="${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    // Insert after header section
    const headerSection = document.querySelector('.container-fluid .row.mb-4');
    if (headerSection) {
        headerSection.insertAdjacentHTML('afterend', alertHtml);

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector(`.alert.${alertClass}`);
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);

        // Scroll to alert
        const alert = document.querySelector(`.alert.${alertClass}`);
        if (alert) {
            alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
}

function showAllData() {
    // Redirect to search page without any filters to show all data
    window.location.href = '{{ route("customer.search") }}?show_all=1';
}
</script>
@endpush
