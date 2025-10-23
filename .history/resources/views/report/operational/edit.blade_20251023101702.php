@extends('layouts.app')

@section('title', 'Edit Data Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Data Pelanggan
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Alert Error -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Form Edit -->
                    <form action="{{ route('report.operational.update', $pelanggan->id_pelanggan) }}"
                          method="POST"
                          id="editPelangganForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- ID Pelanggan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_pelanggan">
                                        ID Pelanggan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('id_pelanggan') is-invalid @enderror"
                                           id="id_pelanggan"
                                           name="id_pelanggan"
                                           value="{{ old('id_pelanggan', $pelanggan->id_pelanggan) }}"
                                           readonly>
                                    @error('id_pelanggan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">ID Pelanggan tidak dapat diubah</small>
                                </div>
                            </div>

                            <!-- Nama Pelanggan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_pelanggan">
                                        Nama Pelanggan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('nama_pelanggan') is-invalid @enderror"
                                           id="nama_pelanggan"
                                           name="nama_pelanggan"
                                           value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}"
                                           placeholder="Masukkan nama pelanggan"
                                           required>
                                    @error('nama_pelanggan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Bandwidth -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bandwidth">
                                        Bandwidth <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('bandwidth') is-invalid @enderror"
                                            id="bandwidth"
                                            name="bandwidth"
                                            required>
                                        <option value="">-- Pilih Bandwidth --</option>
                                        <option value="10 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '10 Mbps' ? 'selected' : '' }}>10 Mbps</option>
                                        <option value="20 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '20 Mbps' ? 'selected' : '' }}>20 Mbps</option>
                                        <option value="30 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '30 Mbps' ? 'selected' : '' }}>30 Mbps</option>
                                        <option value="50 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '50 Mbps' ? 'selected' : '' }}>50 Mbps</option>
                                        <option value="100 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '100 Mbps' ? 'selected' : '' }}>100 Mbps</option>
                                    </select>
                                    @error('bandwidth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nomor_telepon">
                                        Nomor Telepon <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('nomor_telepon') is-invalid @enderror"
                                           id="nomor_telepon"
                                           name="nomor_telepon"
                                           value="{{ old('nomor_telepon', $pelanggan->nomor_telepon) }}"
                                           placeholder="08xxxxxxxxxx"
                                           required>
                                    @error('nomor_telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kode FAT -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kode_fat">Kode FAT</label>
                                    <input type="text"
                                           class="form-control @error('kode_fat') is-invalid @enderror"
                                           id="kode_fat"
                                           name="kode_fat"
                                           value="{{ old('kode_fat', $pelanggan->kode_fat) }}"
                                           placeholder="FAT-XXX-XXX">
                                    @error('kode_fat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Provinsi -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="provinsi">
                                        Provinsi <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('provinsi') is-invalid @enderror"
                                            id="provinsi"
                                            name="provinsi"
                                            required>
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach($regionData as $prov => $kabupatens)
                                            <option value="{{ $prov }}" {{ old('provinsi', $pelanggan->provinsi) == $prov ? 'selected' : '' }}>
                                                {{ $prov }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provinsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kabupaten -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kabupaten">
                                        Kabupaten/Kota <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('kabupaten') is-invalid @enderror"
                                            id="kabupaten"
                                            name="kabupaten"
                                            required>
                                        <option value="">-- Pilih Kabupaten --</option>
                                        @if(old('provinsi', $pelanggan->provinsi) && isset($regionData[old('provinsi', $pelanggan->provinsi)]))
                                            @foreach($regionData[old('provinsi', $pelanggan->provinsi)] as $kab)
                                                <option value="{{ $kab }}" {{ old('kabupaten', $pelanggan->kabupaten) == $kab ? 'selected' : '' }}>
                                                    {{ $kab }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('kabupaten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Cluster -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cluster">
                                        Cluster <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('cluster') is-invalid @enderror"
                                            id="cluster"
                                            name="cluster"
                                            required>
                                        <option value="">-- Pilih Cluster --</option>
                                        <option value="CLUSTER A" {{ old('cluster', $pelanggan->cluster) == 'CLUSTER A' ? 'selected' : '' }}>CLUSTER A</option>
                                        <option value="CLUSTER B" {{ old('cluster', $pelanggan->cluster) == 'CLUSTER B' ? 'selected' : '' }}>CLUSTER B</option>
                                        <option value="CLUSTER C" {{ old('cluster', $pelanggan->cluster) == 'CLUSTER C' ? 'selected' : '' }}>CLUSTER C</option>
                                        <option value="CLUSTER D" {{ old('cluster', $pelanggan->cluster) == 'CLUSTER D' ? 'selected' : '' }}>CLUSTER D</option>
                                    </select>
                                    @error('cluster')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Alamat -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="alamat">
                                        Alamat <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror"
                                              id="alamat"
                                              name="alamat"
                                              rows="3"
                                              placeholder="Masukkan alamat lengkap"
                                              required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Latitude -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">
                                        Latitude
                                        <small class="text-muted">(Koordinat GPS)</small>
                                    </label>
                                    <input type="number"
                                           step="0.0000001"
                                           class="form-control @error('latitude') is-invalid @enderror"
                                           id="latitude"
                                           name="latitude"
                                           value="{{ old('latitude', $pelanggan->latitude) }}"
                                           placeholder="-8.409518">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Contoh: -8.409518</small>
                                </div>
                            </div>

                            <!-- Longitude -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">
                                        Longitude
                                        <small class="text-muted">(Koordinat GPS)</small>
                                    </label>
                                    <input type="number"
                                           step="0.0000001"
                                           class="form-control @error('longitude') is-invalid @enderror"
                                           id="longitude"
                                           name="longitude"
                                           value="{{ old('longitude', $pelanggan->longitude) }}"
                                           placeholder="115.188916">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Contoh: 115.188916</small>
                                </div>
                            </div>
                        </div>

                        <!-- Button Get Current Location -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info btn-sm" id="getCurrentLocation">
                                    <i class="fas fa-map-marker-alt"></i> Ambil Lokasi Saat Ini
                                </button>
                                <small class="text-muted ml-2" id="locationStatus"></small>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('report.operational.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Data region untuk dropdown dinamis
    const regionData = @json($regionData);

    // Event handler untuk perubahan provinsi
    $('#provinsi').change(function() {
        const selectedProvinsi = $(this).val();
        const kabupatenSelect = $('#kabupaten');

        // Reset kabupaten dropdown
        kabupatenSelect.empty();
        kabupatenSelect.append('<option value="">-- Pilih Kabupaten --</option>');

        // Populate kabupaten berdasarkan provinsi
        if (selectedProvinsi && regionData[selectedProvinsi]) {
            regionData[selectedProvinsi].forEach(function(kabupaten) {
                kabupatenSelect.append(`<option value="${kabupaten}">${kabupaten}</option>`);
            });
        }
    });

    // Get current location menggunakan HTML5 Geolocation
    $('#getCurrentLocation').click(function() {
        const btn = $(this);
        const status = $('#locationStatus');

        if (!navigator.geolocation) {
            status.text('Browser tidak mendukung geolocation').css('color', 'red');
            return;
        }

        btn.prop('disabled', true);
        status.text('Mengambil lokasi...').css('color', 'blue');

        navigator.geolocation.getCurrentPosition(
            function(position) {
                $('#latitude').val(position.coords.latitude.toFixed(7));
                $('#longitude').val(position.coords.longitude.toFixed(7));
                status.text('Lokasi berhasil diambil!').css('color', 'green');
                btn.prop('disabled', false);
            },
            function(error) {
                let errorMsg = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg = 'Izin lokasi ditolak';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg = 'Lokasi tidak tersedia';
                        break;
                    case error.TIMEOUT:
                        errorMsg = 'Timeout';
                        break;
                    default:
                        errorMsg = 'Error tidak diketahui';
                }
                status.text(errorMsg).css('color', 'red');
                btn.prop('disabled', false);
            }
        );
    });

    // Form validation
    $('#editPelangganForm').submit(function(e) {
        let isValid = true;
        let errorMsg = [];

        // Validate required fields
        if (!$('#nama_pelanggan').val()) {
            errorMsg.push('Nama Pelanggan wajib diisi');
            isValid = false;
        }

        if (!$('#bandwidth').val()) {
            errorMsg.push('Bandwidth wajib dipilih');
            isValid = false;
        }

        if (!$('#nomor_telepon').val()) {
            errorMsg.push('Nomor Telepon wajib diisi');
            isValid = false;
        }

        if (!$('#provinsi').val()) {
            errorMsg.push('Provinsi wajib dipilih');
            isValid = false;
        }

        if (!$('#kabupaten').val()) {
            errorMsg.push('Kabupaten wajib dipilih');
            isValid = false;
        }

        if (!$('#cluster').val()) {
            errorMsg.push('Cluster wajib dipilih');
            isValid = false;
        }

        if (!$('#alamat').val()) {
            errorMsg.push('Alamat wajib diisi');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Error:\n' + errorMsg.join('\n'));
            return false;
        }

        // Confirm before submit
        if (!confirm('Apakah Anda yakin ingin menyimpan perubahan data ini?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
@endsection
