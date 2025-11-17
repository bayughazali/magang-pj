@extends('layouts.app')

@section('content')
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
                    
                    {{-- SALES OTOMATIS DARI USER LOGIN - READONLY --}}
                    <div class="form-group mb-3">
                        <label>Sales</label>
                        <input type="text" 
                               class="form-control bg-light" 
                               value="{{ auth()->user()->name }}" 
                               readonly 
                               style="cursor: not-allowed;">
                        {{-- Hidden input untuk dikirim ke backend --}}
                        <input type="hidden" name="sales" value="{{ auth()->user()->name }}">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Nama sales otomatis terisi dari akun Anda
                        </small>
                    </div>

                    <div class="form-group mb-3">
                        <label>Aktivitas / Kegiatan</label>
                        <input type="text" name="aktivitas" class="form-control" placeholder="Contoh: Kunjungan PT ABC"
                               value="{{ old('aktivitas') }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="{{ old('tanggal', date('Y-m-d')) }}" required>
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
                        <select name="status" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="proses" {{ old('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-save"></i> Simpan Report
                    </button>
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
                                <th width="8%">Status</th>
                                @if(auth()->user()->role === 'admin')
                                    <th width="12%">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $i => $report)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>
                                        <i class="fas fa-user-circle text-primary"></i> 
                                        {{ $report->sales }}
                                    </td>
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
                                    <td>
                                        <span class="badge {{ $report->status == 'selesai' ? 'bg-warning' : 'bg-success' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>

                                    @if(auth()->user()->role === 'admin')
                                        <td>
                                            {{-- <button class="btn btn-warning btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $report->id }}"> --}}
                                                <button type="button" 
                                                class="btn btn-warning btn-sm mb-1" 
                                                data-toggle="modal" 
                                                data-target="#editModal{{ $report->id }}">
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
                                                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> --}}
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                {{-- ADMIN TETAP BISA EDIT NAMA SALES --}}
                                                                <div class="mb-3">
                                                                    <label>Sales</label>
                                                                    <input type="text" name="sales" class="form-control" value="{{ $report->sales }}" required>
                                                                    <small class="text-info">
                                                                        <i class="fas fa-info-circle"></i> Admin dapat mengubah nama sales
                                                                    </small>
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
                                                            <select name="cluster" class="form-control select2-modal" required>
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
                                                            <select name="status" class="form-control" required>
                                                                <option value="selesai" {{ $report->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                                <option value="proses" {{ $report->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Update</button>
                                                        {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button> --}}
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role === 'admin' ? '10' : '9' }}" class="text-center py-4">
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
{{-- <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Evidence Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button> --}}
                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Evidence Photo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    /* Style untuk input readonly */
    .form-control[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
        border: 1px solid #ced4da;
    }
    
    .form-control[readonly]:focus {
        box-shadow: none;
        border-color: #ced4da;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk form utama
    $('.select2').select2({ 
        placeholder: 'Cari cluster…', 
        width: '100%',
        allowClear: true
    });

    // Handle Select2 di modal edit
    $('.modal').on('shown.bs.modal', function () {
        var modal = $(this);
        modal.find('.select2-modal').each(function() {
            $(this).select2({
                placeholder: 'Cari cluster…',
                width: '100%',
                allowClear: true,
                dropdownParent: modal.find('.modal-content')
            });
        });
    });

    // Destroy Select2 saat modal ditutup
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('.select2-modal').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
    });
});

// Fungsi untuk menampilkan gambar di modal
function showImage(src) {
    document.getElementById('modalImage').src = src;
    var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}
</script>
@endpush