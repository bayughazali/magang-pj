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

                <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
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
                    <div class="form-group mb-3">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Jl. Sudirman No. 123"
                               value="{{ old('lokasi') }}" required>
                    </div>

                    {{-- Provinsi --}}
                    <div class="form-group mb-3">
                        <label>Provinsi</label>
                        <select name="provinsi" id="provinsi" class="form-control select2" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach(array_keys($wilayahData) as $provinsi)
                                <option value="{{ $provinsi }}" {{ old('provinsi') == $provinsi ? 'selected' : '' }}>
                                    {{ $provinsi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kabupaten --}}
                    <div class="form-group mb-3">
                        <label>Kabupaten/Kota</label>
                        <select name="kabupaten" id="kabupaten" class="form-control select2" required disabled>
                            <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
                        </select>
                    </div>

                    {{-- Kecamatan --}}
                    <div class="form-group mb-3">
                        <label>Kecamatan</label>
                        <select name="kecamatan" id="kecamatan" class="form-control select2" required disabled>
                            <option value="">-- Pilih Kabupaten Terlebih Dahulu --</option>
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
                                <th width="3%">No</th>
                                <th width="8%">Sales</th>
                                <th width="12%">Aktivitas</th>
                                <th width="8%">Tanggal</th>
                                <th width="10%">Lokasi</th>
                                <th width="8%">Provinsi</th>
                                <th width="10%">Kabupaten</th>
                                <th width="10%">Kecamatan</th>
                                <th width="8%">Evidence</th>
                                <th width="15%">Hasil / Kendala</th>
                                <th width="6%">Status</th>
                                @if(auth()->user()->role === 'admin')
                                    <th width="10%">Aksi</th>
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
                                    <td>{{ Str::limit($report->lokasi, 15) }}</td>
                                    <td>{{ Str::limit($report->provinsi ?? '-', 10) }}</td>
                                    <td>{{ $report->kabupaten ?? '-' }}</td>
                                    <td>{{ $report->kecamatan ?? '-' }}</td>
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
                                                <form action="{{ route('reports.update', $report->id) }}" method="POST" enctype="multipart/form-data" class="editForm">
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
                                                            <label>Lokasi</label>
                                                            <input type="text" name="lokasi" class="form-control" value="{{ $report->lokasi }}" required>
                                                        </div>

                                                        {{-- Provinsi Edit --}}
                                                        <div class="mb-3">
                                                            <label>Provinsi</label>
                                                            <select name="provinsi" class="form-control select2 edit-provinsi" required data-report-id="{{ $report->id }}">
                                                                <option value="">-- Pilih Provinsi --</option>
                                                                @foreach(array_keys($wilayahData) as $provinsi)
                                                                    <option value="{{ $provinsi }}" {{ ($report->provinsi ?? '') == $provinsi ? 'selected' : '' }}>
                                                                        {{ $provinsi }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        {{-- Kabupaten Edit --}}
                                                        <div class="mb-3">
                                                            <label>Kabupaten/Kota</label>
                                                            <select name="kabupaten" class="form-control select2 edit-kabupaten-{{ $report->id }}" required>
                                                                <option value="{{ $report->kabupaten ?? '' }}">{{ $report->kabupaten ?? '-- Pilih Kabupaten --' }}</option>
                                                            </select>
                                                        </div>

                                                        {{-- Kecamatan Edit --}}
                                                        <div class="mb-3">
                                                            <label>Kecamatan</label>
                                                            <select name="kecamatan" class="form-control select2 edit-kecamatan-{{ $report->id }}" required>
                                                                <option value="{{ $report->kecamatan ?? '' }}">{{ $report->kecamatan ?? '-- Pilih Kecamatan --' }}</option>
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
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role === 'admin' ? '12' : '11' }}" class="text-center py-4">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
// Data wilayah dalam JavaScript
const wilayahData = @json($wilayahData);

console.log('Wilayah Data:', wilayahData); // Debug: cek data

$(document).ready(function() {
    console.log('jQuery ready!'); // Debug

    // Initialize Select2 PERTAMA
    $('#provinsi').select2({
        placeholder: 'Pilih Provinsi...',
        width: '100%'
    });

    $('#kabupaten').select2({
        placeholder: 'Pilih Kabupaten/Kota...',
        width: '100%'
    });

    $('#kecamatan').select2({
        placeholder: 'Pilih Kecamatan...',
        width: '100%'
    });

    // === FORM TAMBAH ===
    $('#provinsi').on('select2:select', function(e) {
        const selectedProvinsi = e.params.data.id;
        console.log('Provinsi dipilih:', selectedProvinsi); // Debug

        // Reset & Clear kabupaten
        $('#kabupaten').empty().prop('disabled', true);
        $('#kecamatan').empty().prop('disabled', true);

        $('#kabupaten').append('<option value="">-- Pilih Kabupaten/Kota --</option>');
        $('#kecamatan').append('<option value="">-- Pilih Kecamatan --</option>');

        if (selectedProvinsi && wilayahData[selectedProvinsi]) {
            console.log('Kabupaten tersedia:', Object.keys(wilayahData[selectedProvinsi])); // Debug

            // Populate kabupaten
            const kabupatenList = Object.keys(wilayahData[selectedProvinsi]);
            $.each(kabupatenList, function(index, kab) {
                $('#kabupaten').append($('<option>', {
                    value: kab,
                    text: kab
                }));
            });

            $('#kabupaten').prop('disabled', false);
        }

        // Destroy dan reinit select2
        $('#kabupaten').select2('destroy').select2({
            placeholder: 'Pilih Kabupaten/Kota...',
            width: '100%'
        });

        $('#kecamatan').select2('destroy').select2({
            placeholder: 'Pilih Kecamatan...',
            width: '100%'
        });
    });

    // Event listener untuk kabupaten
    $('#kabupaten').on('select2:select', function(e) {
        const selectedProvinsi = $('#provinsi').val();
        const selectedKabupaten = e.params.data.id;
        console.log('Kabupaten dipilih:', selectedKabupaten); // Debug

        // Reset kecamatan
        $('#kecamatan').empty().prop('disabled', true);
        $('#kecamatan').append('<option value="">-- Pilih Kecamatan --</option>');

        if (selectedProvinsi && selectedKabupaten && wilayahData[selectedProvinsi][selectedKabupaten]) {
            console.log('Kecamatan tersedia:', wilayahData[selectedProvinsi][selectedKabupaten]); // Debug

            // Populate kecamatan
            const kecamatanList = wilayahData[selectedProvinsi][selectedKabupaten];
            $.each(kecamatanList, function(index, kec) {
                $('#kecamatan').append($('<option>', {
                    value: kec,
                    text: kec
                }));
            });

            $('#kecamatan').prop('disabled', false);
        }

        // Destroy dan reinit select2
        $('#kecamatan').select2('destroy').select2({
            placeholder: 'Pilih Kecamatan...',
            width: '100%'
        });
    });

    // === FORM EDIT ===
    $('.edit-provinsi').each(function() {
        const editProvinsi = $(this);
        const reportId = editProvinsi.data('report-id');
        const editKabupaten = $(`.edit-kabupaten-${reportId}`);
        const editKecamatan = $(`.edit-kecamatan-${reportId}`);

        // Initialize select2 untuk modal edit
        editProvinsi.select2({
            placeholder: 'Pilih Provinsi...',
            width: '100%',
            dropdownParent: editProvinsi.closest('.modal')
        });

        editKabupaten.select2({
            placeholder: 'Pilih Kabupaten...',
            width: '100%',
            dropdownParent: editKabupaten.closest('.modal')
        });

        editKecamatan.select2({
            placeholder: 'Pilih Kecamatan...',
            width: '100%',
            dropdownParent: editKecamatan.closest('.modal')
        });

        // Event change provinsi edit
        editProvinsi.on('select2:select', function(e) {
            const selectedProvinsi = e.params.data.id;

            editKabupaten.empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>');
            editKecamatan.empty().append('<option value="">-- Pilih Kecamatan --</option>');

            if (selectedProvinsi && wilayahData[selectedProvinsi]) {
                const kabupatenList = Object.keys(wilayahData[selectedProvinsi]);
                $.each(kabupatenList, function(index, kab) {
                    editKabupaten.append($('<option>', {
                        value: kab,
                        text: kab
                    }));
                });

                editKabupaten.select2('destroy').select2({
                    placeholder: 'Pilih Kabupaten...',
                    width: '100%',
                    dropdownParent: editKabupaten.closest('.modal')
                });
            }
        });

        // Event change kabupaten edit
        editKabupaten.on('select2:select', function(e) {
            const selectedProvinsi = editProvinsi.val();
            const selectedKabupaten = e.params.data.id;

            editKecamatan.empty().append('<option value="">-- Pilih Kecamatan --</option>');

            if (selectedProvinsi && selectedKabupaten && wilayahData[selectedProvinsi][selectedKabupaten]) {
                const kecamatanList = wilayahData[selectedProvinsi][selectedKabupaten];
                $.each(kecamatanList, function(index, kec) {
                    editKecamatan.append($('<option>', {
                        value: kec,
                        text: kec
                    }));
                });

                editKecamatan.select2('destroy').select2({
                    placeholder: 'Pilih Kecamatan...',
                    width: '100%',
                    dropdownParent: editKecamatan.closest('.modal')
                });
            }
        });
    });
});

function showImage(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

@endsection
