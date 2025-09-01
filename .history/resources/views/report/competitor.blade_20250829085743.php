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

    <!-- Results Table -->
    {{-- ... bagian tabel tetap sama persis ... --}}
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
