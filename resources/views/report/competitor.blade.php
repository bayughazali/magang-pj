@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header Section -->
    {{-- ... bagian header & search form tetap sama persis ... --}}

    <!-- Quick Action Buttons -->
    @if(isset($pelanggans) && $pelanggans->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('customer.map') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-map-marked-alt me-1"></i>Lihat di Peta
                            </a>
                        </div>
                        <small class="text-muted">
                            Menampilkan {{ $pelanggans->firstItem() }}-{{ $pelanggans->lastItem() }} dari {{ $pelanggans->total() }} data
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

<<<<<<< HEAD
    <!-- Results Table -->
    {{-- ... bagian tabel tetap sama persis ... --}}
=======
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

<<<<<<< Updated upstream
        <div class="mt-4" id="saveBtn" style="display: none;">
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
        </div>
      </form>

      <!-- TABEL HASIL -->
      <hr>
      <h5 class="mb-3">Data Competitor</h5>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
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
                <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            {{-- 🔹 Looping data competitor --}}
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
                <td>
                    {{-- 🔹 Edit --}}
                    <a href="{{ route('competitor.edit', $item->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i>
                    </a>

                    {{-- 🔹 Delete --}}
                    <form action="{{ route('competitor.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data?')">
                        <i class="fas fa-trash"></i>
                    </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center">Belum ada data competitor</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

=======
        <!-- <div class="mt-4" id="saveBtn" style="display: none;">
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
        </div>
      </form> -->
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
            <th>Aksi</th>
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
          </tr>
          @empty
          <tr>
            <td colspan="10">Belum ada data competitor</td>
          </tr>
          @endforelse
        </tbody>
      </table>
>>>>>>> Stashed changes
    </div>
  </div>
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
</div>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data pelanggan:</p>
                <div class="alert alert-warning">
                    <strong id="customerName"></strong>
                </div>
                <p class="text-muted"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<<<<<<< HEAD
@endsection

@push('scripts')
<script>
// Global variables
let currentDeleteId = null;

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('filter_query');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }

    if (!document.querySelector('meta[name="csrf-token"]')) {
        const metaTag = document.createElement('meta');
        metaTag.name = 'csrf-token';
        metaTag.content = '{{ csrf_token() }}';
        document.head.appendChild(metaTag);
    }

    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (currentDeleteId) {
                performDelete(currentDeleteId);
                const deleteModalEl = document.getElementById('deleteModal');
                const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
                if (deleteModal) {
                    deleteModal.hide();
                }
            }
        });
    }
});

function showBasicSearch() {
    document.getElementById('basicSearchForm').style.display = 'block';
    document.getElementById('advancedSearchForm').style.display = 'none';
    const buttons = document.querySelectorAll('.btn-group-sm .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function showAdvancedSearch() {
    document.getElementById('basicSearchForm').style.display = 'none';
    document.getElementById('advancedSearchForm').style.display = 'block';
    const buttons = document.querySelectorAll('.btn-group-sm .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function deletePelanggan(id, nama) {
    currentDeleteId = id;
    document.getElementById('customerName').textContent = nama;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function performDelete(id) {
    const deleteBtn = document.getElementById('confirmDelete');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
    deleteBtn.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        showAlert('CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.', 'error');
        resetDeleteButton(deleteBtn, originalText);
        return;
    }

    // ✅ Pakai backtick untuk template literal
    const deleteUrl = `/customer/${id}`;

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
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert(data.message || 'Data pelanggan berhasil dihapus!', 'success');
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
            const currentPage = {{ $pelanggans->currentPage() ?? 1 }};
            const perPage = {{ $pelanggans->perPage() ?? 10 }};
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

    const headerTotal = document.querySelector('.text-end h5');
    if (headerTotal) {
        const currentHeaderTotal = parseInt(headerTotal.textContent) - 1;
        headerTotal.textContent = currentHeaderTotal;
    }

    if (remainingRows === 0) {
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
}

function showAlert(message, type = 'success') {
    document.querySelectorAll('.alert').forEach(alert => alert.remove());

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    const headerSection = document.querySelector('.container-fluid .row.mb-4');
    if (headerSection) {
        headerSection.insertAdjacentHTML('afterend', alertHtml);

        setTimeout(() => {
            const alert = document.querySelector(\`.alert.${alertClass}\`);
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);

        const alert = document.querySelector(\`.alert.${alertClass}\`);
        if (alert) {
            alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
}

function viewLocation(lat, lng, nama) {
    if (!lat || !lng) {
        showAlert('Koordinat tidak tersedia untuk pelanggan ini', 'error');
        return;
    }

    const url = `https://www.google.com/maps?q=${lat},${lng}&z=15&t=m&hl=id`;
    const mapWindow = window.open(url, '_blank');

    if (!mapWindow || mapWindow.closed || typeof mapWindow.closed == 'undefined') {
        const coordText = `${lat}, ${lng}`;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(coordText).then(() => {
                showAlert(`Popup diblokir! Koordinat ${coordText} telah disalin ke clipboard. Paste di Google Maps secara manual.`, 'error');
            }).catch(() => {
                showAlert(`Popup diblokir! Koordinat: ${coordText}. Salin manual ke Google Maps.`, 'error');
            });
        } else {
            showAlert(`Popup diblokir! Koordinat: ${coordText}. Salin manual ke Google Maps.`, 'error');
        }
    }
}

function showAllData() {
    const form = document.querySelector('#basicSearchForm form');
    const filterField = form.querySelector('[name="filter_field"]');
    const filterQuery = form.querySelector('[name="filter_query"]');

    filterField.value = '';
    filterQuery.value = '';
    form.submit();
}
</script>
@endpush
=======
  // tombol hapus
  div.querySelector(".removeRow").addEventListener("click", function() {
    div.remove();
  });
});
</script>
<<<<<<< Updated upstream
@endsection
=======

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
>>>>>>> Stashed changes
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
