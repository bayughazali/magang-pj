@extends('layouts.app')

@section('content')
<style>
/* Toggle Switch CSS */
.status-toggle {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 28px;
}

.status-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.status-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ffc107;
    transition: .4s;
    border-radius: 28px;
}

.status-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .status-slider {
    background-color: #28a745;
}

input:checked + .status-slider:before {
    transform: translateX(32px);
}

.status-label {
    font-size: 11px;
    font-weight: bold;
    display: block;
    text-align: center;
    margin-top: 2px;
}
</style>

<div class="row">
    {{-- Form Tambah Report --}}
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Tambah Report Activity</div>
            <div class="card-body">
                {{-- pesan sukses --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- pesan error --}}
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Sales</label>
                        <input type="text" name="sales" class="form-control" placeholder="Nama Sales"
                               value="{{ old('sales') }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Aktivitas / Kegiatan</label>
                        <input type="text" name="aktivitas" class="form-control" placeholder="Contoh: Kunjungan PT ABC"
                               value="{{ old('aktivitas') }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="{{ old('tanggal') }}" required>
                    </div>

                    {{-- Blok Cluster --}}
                    <div class="form-group mb-3">
                        <label>Cluster</label>
                        <select name="cluster" class="form-control select2" required>
                            <option value="">-- Pilih Cluster --</option>
                            @foreach($competitors as $provinsi => $kabupatens)
                                <optgroup label="{{ $provinsi }}">
                                    @foreach($kabupatens as $namaKab => $kecamatans)
                                        @foreach($kecamatans as $namaKec)
                                            @php $value = "$provinsi - $namaKab - $namaKec"; @endphp
                                            <option value="{{ $value }}" {{ old('cluster') == $value ? 'selected' : '' }}>
                                                {{ $namaKab }} - {{ $namaKec }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Evidence (Foto Progress)</label>
                        <input type="file" name="evidence" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG. Max: 2MB</small>
                    </div>
                    <div class="form-group mb-3">
                        <label>Hasil / Kendala</label>
                        <textarea name="hasil_kendala" class="form-control" rows="3"
                                  placeholder="Tuliskan hasil kegiatan atau kendala yang ditemui">{{ old('hasil_kendala') }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <div class="d-flex align-items-center">
                            <span class="me-2">Proses</span>
                            <label class="status-toggle">
                                <input type="checkbox" name="status_toggle" value="selesai" {{ old('status') == 'selesai' ? 'checked' : '' }}>
                                <span class="status-slider"></span>
                            </label>
                            <span class="ms-2">Selesai</span>
                            <input type="hidden" name="status" value="{{ old('status', 'proses') }}" id="status_input">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Daftar Report --}}
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Daftar Report Activity ({{ count($reports) }} data)</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Sales</th>
                                <th width="15%">Aktivitas</th>
                                <th width="10%">Tanggal</th>
                                <th width="8%">Lokasi (Cluster)</th>
                                <th width="10%">Evidence</th>
                                <th width="20%">Hasil / Kendala</th>
                                <th width="10%">Status</th>
                                @if(auth()->user()->role === 'admin')
                                    <th width="12%">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $i => $report)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $report->sales }}</td>
                                    <td>{{ Str::limit($report->aktivitas, 20) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($report->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $report->cluster }}</td>
                                    <td class="text-center">
                                        @if($report->evidence && Storage::disk('public')->exists($report->evidence))
                                            <img src="{{ asset('storage/'.$report->evidence) }}"
                                                 width="60" height="60" class="rounded"
                                                 style="object-fit: cover; cursor: pointer;"
                                                 onclick="showImage('{{ asset('storage/'.$report->evidence) }}')"
                                                 title="Klik untuk memperbesar"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                            <span style="display: none; color: #dc3545;">❌ Error</span>
                                        @elseif($report->evidence)
                                            <span class="text-warning" title="File tidak ditemukan">⚠ Missing</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($report->hasil_kendala ?? '-', 30) }}</td>
                                    <td class="text-center">
                                        <label class="status-toggle">
                                            <input type="checkbox"
                                                   data-report-id="{{ $report->id }}"
                                                   class="status-toggle-input"
                                                   {{ $report->status == 'selesai' ? 'checked' : '' }}
                                                   {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                            <span class="status-slider"></span>
                                        </label>
                                        <span class="status-label text-{{ $report->status == 'selesai' ? 'success' : 'warning' }}">
                                            {{ $report->status == 'selesai' ? 'SELESAI' : 'PROSES' }}
                                        </span>
                                    </td>

                                    @if(auth()->user()->role === 'admin')
                                        <td>
                                            <button class="btn btn-warning btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $report->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Yakin hapus data ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>

                                @if(auth()->user()->role === 'admin')
                                    <div class="modal fade" id="editModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form action="{{ route('reports.update', $report->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Report - {{ $report->sales }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label>Sales</label>
                                                                    <input type="text" name="sales" class="form-control" value="{{ $report->sales }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label>Tanggal</label>
                                                                    <input type="date" name="tanggal" class="form-control" value="{{ $report->tanggal }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Aktivitas</label>
                                                            <input type="text" name="aktivitas" class="form-control" value="{{ $report->aktivitas }}" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label>Cluster</label>
                                                            <select name="cluster" class="form-control select2" required style="width: 100%;">
                                                                <option value="">-- Pilih Cluster --</option>
                                                                @foreach($competitors as $provinsi => $kabupatens)
                                                                    <optgroup label="{{ $provinsi }}">
                                                                        @foreach($kabupatens as $namaKab => $kecamatans)
                                                                            @foreach($kecamatans as $namaKec)
                                                                                @php $value = "$provinsi - $namaKab - $namaKec"; @endphp
                                                                                <option value="{{ $value }}" {{ $report->cluster == $value ? 'selected' : '' }}>
                                                                                    {{ $namaKab }} - {{ $namaKec }}
                                                                                </option>
                                                                            @endforeach
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label>Evidence (Opsional)</label><br>
                                                            @if($report->evidence)
                                                                <div class="mb-2">
                                                                    <small class="text-muted">Gambar saat ini:</small><br>
                                                                    <img src="{{ asset('storage/'.$report->evidence) }}" width="100" class="rounded">
                                                                </div>
                                                            @endif
                                                            <input type="file" name="evidence" class="form-control" accept="image/*">
                                                            <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Hasil / Kendala</label>
                                                            <textarea name="hasil_kendala" class="form-control" rows="3">{{ $report->hasil_kendala }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Status</label>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">Proses</span>
                                                                <label class="status-toggle">
                                                                    <input type="checkbox"
                                                                           name="status_toggle_edit"
                                                                           value="selesai"
                                                                           {{ $report->status == 'selesai' ? 'checked' : '' }}
                                                                           id="status_toggle_edit_{{ $report->id }}">
                                                                    <span class="status-slider"></span>
                                                                </label>
                                                                <span class="ms-2">Selesai</span>
                                                                <input type="hidden" name="status" value="{{ $report->status }}" id="status_input_edit_{{ $report->id }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Update</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role === 'admin' ? '9' : '8' }}" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                            Belum ada report activity<br>
                                            <small>Tambahkan report pertama Anda menggunakan form di samping</small>
                                        </div>
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

{{-- Modal untuk melihat gambar --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Evidence Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Initialize Select2
    $('.select2').select2({ placeholder: 'Cari cluster…', width: '100%' });

    // Handle Select2 in modals
    document.querySelectorAll('.modal').forEach(function(modalElement) {
        modalElement.addEventListener('shown.bs.modal', function () {
            var selectInModal = modalElement.querySelector('.select2');
            if (selectInModal) {
                $(selectInModal).select2('destroy');
                $(selectInModal).select2({
                    placeholder: 'Cari cluster…',
                    width: '100%',
                    dropdownParent: $(modalElement).find('.modal-content')
                });
            }
        });
    });

    // Handle status toggle on form tambah
    const statusToggle = document.querySelector('input[name="status_toggle"]');
    const statusInput = document.getElementById('status_input');

    if (statusToggle && statusInput) {
        statusToggle.addEventListener('change', function() {
            statusInput.value = this.checked ? 'selesai' : 'proses';
        });
    }

    // Handle status toggle on edit modals
    document.querySelectorAll('input[name="status_toggle_edit"]').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const reportId = this.id.replace('status_toggle_edit_', '');
            const hiddenInput = document.getElementById('status_input_edit_' + reportId);
            if (hiddenInput) {
                hiddenInput.value = this.checked ? 'selesai' : 'proses';
            }
        });
    });

    // Handle quick status toggle in table (dengan AJAX)
    document.querySelectorAll('.status-toggle-input').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            if (this.disabled) return;

            const reportId = this.getAttribute('data-report-id');
            const newStatus = this.checked ? 'selesai' : 'proses';
            const statusLabel = this.closest('td').querySelector('.status-label');

            // Update UI immediately
            if (statusLabel) {
                statusLabel.textContent = newStatus.toUpperCase();
                statusLabel.className = 'status-label text-' + (newStatus === 'selesai' ? 'success' : 'warning');
            }

            // Send AJAX request to update status
            fetch(`/reports/${reportId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert on error
                    this.checked = !this.checked;
                    if (statusLabel) {
                        const revertStatus = this.checked ? 'selesai' : 'proses';
                        statusLabel.textContent = revertStatus.toUpperCase();
                        statusLabel.className = 'status-label text-' + (revertStatus === 'selesai' ? 'success' : 'warning');
                    }
                    alert('Gagal mengubah status');
                }
            })
            .catch(error => {
                // Revert on error
                this.checked = !this.checked;
                if (statusLabel) {
                    const revertStatus = this.checked ? 'selesai' : 'proses';
                    statusLabel.textContent = revertStatus.toUpperCase();
                    statusLabel.className = 'status-label text-' + (revertStatus === 'selesai' ? 'success' : 'warning');
                }
                alert('Terjadi kesalahan');
            });
        });
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showImage(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

@endsection
