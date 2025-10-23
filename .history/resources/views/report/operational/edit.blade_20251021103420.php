@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">
                <i class="fas fa-edit"></i> Edit Data Pelanggan
            </h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- FORM EDIT --}}
            <form action="{{ route('report.operational.update', $pelanggan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">ID Pelanggan</label>
                        <input type="text"
                            class="form-control bg-light"
                            value="{{ $pelanggan->id_pelanggan }}"
                            readonly>
                        <small class="text-muted">ID tidak dapat diubah</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Nama Pelanggan *</label>
                        <input type="text"
                            name="nama_pelanggan"
                            class="form-control"
                            value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}"
                            required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Bandwidth *</label>
                        <select name="bandwidth" class="form-control" required>
                            <option value="">-- Pilih Kecepatan --</option>
                            <option value="10 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '10 Mbps' ? 'selected' : '' }}>10 Mbps</option>
                            <option value="20 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '20 Mbps' ? 'selected' : '' }}>20 Mbps</option>
                            <option value="50 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '50 Mbps' ? 'selected' : '' }}>50 Mbps</option>
                            <option value="100 Mbps" {{ old('bandwidth', $pelanggan->bandwidth) == '100 Mbps' ? 'selected' : '' }}>100 Mbps</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Nomor Telepon *</label>
                        <input type="text"
                            name="nomor_telepon"
                            class="form-control"
                            value="{{ old('nomor_telepon', $pelanggan->nomor_telepon) }}"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Provinsi *</label>
                        <select name="provinsi" id="provinsi" class="form-control" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($regionData as $provinsi => $kabupaten)
                                <option value="{{ $provinsi }}" {{ old('provinsi', $pelanggan->provinsi) == $provinsi ? 'selected' : '' }}>
                                    {{ $provinsi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Kabupaten/Kota *</label>
                        <select name="kabupaten" id="kabupaten" class="form-control" required>
                            <option value="">-- Pilih Kabupaten --</option>
                            <option value="{{ $pelanggan->kabupaten }}" selected>{{ $pelanggan->kabupaten }}</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Kode FAT</label>
                        <input type="text"
                            id="kode_fat"
                            name="kode_fat"
                            class="form-control fat-code-field"
                            value="{{ old('kode_fat', $pelanggan->kode_fat) }}"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Alamat *</label>
                        <textarea name="alamat"
                            rows="2"
                            class="form-control"
                            required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cluster *</label>
                        <select name="cluster" class="form-control" required>
                            <option value="">-- Pilih Cluster --</option>
                            <option value="Cluster A" {{ old('cluster', $pelanggan->cluster) == 'Cluster A' ? 'selected' : '' }}>Cluster A</option>
                            <option value="Cluster B" {{ old('cluster', $pelanggan->cluster) == 'Cluster B' ? 'selected' : '' }}>Cluster B</option>
                            <option value="Cluster C" {{ old('cluster', $pelanggan->cluster) == 'Cluster C' ? 'selected' : '' }}>Cluster C</option>
                            <option value="Cluster D" {{ old('cluster', $pelanggan->cluster) == 'Cluster D' ? 'selected' : '' }}>Cluster D</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Latitude</label>
                        <input type="text"
                            id="latitude"
                            name="latitude"
                            class="form-control"
                            value="{{ old('latitude', $pelanggan->latitude) }}"
                            readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Longitude</label>
                        <input type="text"
                            id="longitude"
                            name="longitude"
                            class="form-control"
                            value="{{ old('longitude', $pelanggan->longitude) }}"
                            readonly>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-success me-2">
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

{{-- Styling FAT Code Field --}}
<style>
.fat-code-field {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #28a745;
    font-weight: bold;
    color: #28a745;
    font-family: 'Courier New', monospace;
    text-align: center;
}
</style>

{{-- JavaScript untuk Auto-populate Kabupaten & FAT --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinsiSelect = document.getElementById('provinsi');
    const kabupatenSelect = document.getElementById('kabupaten');
    const kodeFatInput = document.getElementById('kode_fat');

    // Event handler saat provinsi dipilih
    provinsiSelect.addEventListener('change', function() {
        const provinsi = this.value;

        kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
        kabupatenSelect.disabled = true;
        kodeFatInput.value = '';

        if (provinsi) {
            const url = `/report/operational/get-kabupaten?provinsi=${encodeURIComponent(provinsi)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.kabupaten && Array.isArray(data.kabupaten)) {
                        data.kabupaten.forEach(kab => {
                            const option = document.createElement('option');
                            option.value = kab;
                            option.textContent = kab;
                            kabupatenSelect.appendChild(option);
                        });
                        kabupatenSelect.disabled = false;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Event handler saat kabupaten dipilih
    kabupatenSelect.addEventListener('change', function() {
        const provinsi = provinsiSelect.value;
        const kabupaten = this.value;

        if (provinsi && kabupaten) {
            const url = `/report/operational/get-kode-fat?provinsi=${encodeURIComponent(provinsi)}&kabupaten=${encodeURIComponent(kabupaten)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.kode_fat) {
                        kodeFatInput.value = data.kode_fat;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
});
</script>
@endsection
