@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Data Pelanggan</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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
            <form action="{{ route('report.operational.update', $pelanggan->id_pelanggan) }}" method="POST" class="mb-4">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- ⭐ PERBAIKAN: Tambahkan name="id_pelanggan" agar ter-submit ⭐ --}}
                    <div class="col-md-3">
                        <label class="form-label">ID Pelanggan</label>
                        <input type="text"
                            name="id_pelanggan"
                            class="form-control bg-light fw-bold text-center"
                            value="{{ $pelanggan->id_pelanggan }}"
                            readonly>
                        <small class="text-muted">ID tidak dapat diubah</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Nama Pelanggan *</label>
                        <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" required>
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
                        <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon', $pelanggan->nomor_telepon) }}" required>
                    </div>

                    {{-- FIELD PROVINSI --}}
                    <div class="col-md-4">
                        <label class="form-label">Provinsi *</label>
                        <select name="provinsi" id="provinsi" class="form-control" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach ($regionData as $provinsi => $kabupatenList)
                                <option value="{{ $provinsi }}" {{ old('provinsi', $pelanggan->provinsi) == $provinsi ? 'selected' : '' }}>
                                    {{ $provinsi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FIELD KABUPATEN --}}
                    <div class="col-md-4">
                        <label class="form-label">Kabupaten/Kota *</label>
                        <select name="kabupaten" id="kabupaten" class="form-control" required>
                            <option value="">-- Pilih Kabupaten --</option>
                        </select>
                    </div>

                    {{-- FIELD FAT YANG OTOMATIS --}}
                    <div class="col-md-4">
                        <label class="form-label">Kode FAT</label>
                        <input type="text" id="kode_fat" name="kode_fat" class="form-control fat-code-field" placeholder="Akan terisi otomatis..." value="{{ old('kode_fat', $pelanggan->kode_fat) }}" readonly>
                        <small class="text-muted">Kode FAT akan muncul setelah memilih provinsi dan kabupaten</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Alamat *</label>
                        <textarea name="alamat" rows="2" class="form-control" required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                    </div>

                    {{-- <div class="col-md-3">
                        <label class="form-label">Cluster *</label>
                        <select name="cluster" class="form-control" required>
                            <option value="">-- Pilih Cluster --</option>
                            <option value="Cluster A" {{ old('cluster', $pelanggan->cluster) == 'Cluster A' ? 'selected' : '' }}>Cluster A</option>
                            <option value="Cluster B" {{ old('cluster', $pelanggan->cluster) == 'Cluster B' ? 'selected' : '' }}>Cluster B</option>
                            <option value="Cluster C" {{ old('cluster', $pelanggan->cluster) == 'Cluster C' ? 'selected' : '' }}>Cluster C</option>
                            <option value="Cluster D" {{ old('cluster', $pelanggan->cluster) == 'Cluster D' ? 'selected' : '' }}>Cluster D</option>
                        </select>
                    </div> --}}

                    <div class="col-md-3">
                        <label class="form-label">Latitude *</label>
                        <input type="text" id="latitude" name="latitude" class="form-control" placeholder="-8.409518" value="{{ old('latitude', $pelanggan->latitude) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Longitude *</label>
                        <input type="text" id="longitude" name="longitude" class="form-control" placeholder="115.188916" value="{{ old('longitude', $pelanggan->longitude) }}" required>
                    </div>

                    {{-- ⭐ BUTTON AMBIL LOKASI SAAT INI - DITAMBAHKAN DI SINI ⭐ --}}
                    <div class="col-md-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-info w-100" id="getCurrentLocation">
                            <i class="fas fa-map-marker-alt me-2"></i> Ambil Lokasi Saat Ini
                        </button>
                        <small id="locationStatus" class="text-muted d-block mt-1"></small>
                    </div>

                    <div class="col-12 mt-3">
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

{{-- Custom CSS --}}
<style>
/* Styling khusus untuk field kode FAT */
.fat-code-field {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #ffc107;
    font-weight: bold;
    color: #ffc107;
    font-family: 'Courier New', monospace;
    text-align: center;
    letter-spacing: 1px;
}

.fat-code-field:focus {
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.4);
    border-color: #ffc107;
    background: linear-gradient(135deg, #fff3cd 0%, #ffecb5 100%);
}

/* Animation untuk update kode FAT */
.fat-updated {
    animation: fatUpdate 1.2s ease-in-out;
}

@keyframes fatUpdate {
    0% {
        transform: scale(1);
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    25% {
        transform: scale(1.05);
        background: linear-gradient(135deg, #fff3cd 0%, #ffecb5 100%);
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.6);
    }
    50% {
        transform: scale(1.08);
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border-color: #17a2b8;
        color: #17a2b8;
    }
    75% {
        transform: scale(1.05);
        background: linear-gradient(135deg, #fff3cd 0%, #ffecb5 100%);
        border-color: #ffc107;
        color: #ffc107;
    }
    100% {
        transform: scale(1);
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Styling untuk button Ambil Lokasi */
#getCurrentLocation {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

#getCurrentLocation:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4);
}

#getCurrentLocation:active {
    transform: translateY(0);
}

#getCurrentLocation.loading {
    background: linear-gradient(90deg, #17a2b8 0%, #138496 50%, #17a2b8 100%);
    background-size: 200% 100%;
    animation: loading-gradient 1.5s ease infinite;
}

@keyframes loading-gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

#locationStatus {
    min-height: 18px;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Coordinate fields highlight when updated */
.coordinate-updated {
    animation: coordinateHighlight 0.8s ease;
}

@keyframes coordinateHighlight {
    0% {
        background-color: #fff;
        transform: scale(1);
    }
    50% {
        background-color: #d1ecf1;
        transform: scale(1.02);
        box-shadow: 0 0 10px rgba(23, 162, 184, 0.5);
    }
    100% {
        background-color: #fff;
        transform: scale(1);
    }
}
</style>

<script>
// JAVASCRIPT UNTUK AUTO DROPDOWN DAN FAT - EDIT PAGE VERSION
document.addEventListener('DOMContentLoaded', function() {
    const provinsiSelect = document.getElementById('provinsi');
    const kabupatenSelect = document.getElementById('kabupaten');
    const kodeFatInput = document.getElementById('kode_fat');

    // Data untuk pre-populate
    const oldProvinsi = '{{ old("provinsi", $pelanggan->provinsi) }}';
    const oldKabupaten = '{{ old("kabupaten", $pelanggan->kabupaten) }}';
    const oldKodeFat = '{{ old("kode_fat", $pelanggan->kode_fat) }}';

    console.log('Edit Page - Initial values:', {
        provinsi: oldProvinsi,
        kabupaten: oldKabupaten,
        kode_fat: oldKodeFat
    });

    // Event handler saat provinsi dipilih
    provinsiSelect.addEventListener('change', function() {
        const provinsi = this.value;
        console.log('Provinsi selected:', provinsi);

        // Reset kabupaten dan kode FAT
        kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
        kabupatenSelect.disabled = true;
        kodeFatInput.value = '';

        if (provinsi) {
            const url = `/report/operational/get-kabupaten?provinsi=${encodeURIComponent(provinsi)}`;
            console.log('Fetching kabupaten URL:', url);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Kabupaten response:', data);

                    kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';

                    if (data.kabupaten && Array.isArray(data.kabupaten) && data.kabupaten.length > 0) {
                        data.kabupaten.forEach(kab => {
                            const option = document.createElement('option');
                            option.value = kab;
                            option.textContent = kab;
                            kabupatenSelect.appendChild(option);
                        });
                        kabupatenSelect.disabled = false;
                        showNotification('Kabupaten berhasil dimuat!', 'success');

                        // Restore old kabupaten value if available
                        if (oldKabupaten && provinsi === oldProvinsi) {
                            kabupatenSelect.value = oldKabupaten;
                            // Trigger kabupaten change to load FAT code
                            const event = new Event('change');
                            kabupatenSelect.dispatchEvent(event);
                        }
                    } else {
                        kabupatenSelect.innerHTML = '<option value="">Tidak ada kabupaten</option>';
                        showNotification('Tidak ada data kabupaten untuk provinsi ini', 'warning');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    kabupatenSelect.innerHTML = '<option value="">Error loading data</option>';
                    showNotification(`Gagal memuat data kabupaten: ${error.message}`, 'error');
                });
        }
    });

    // Event handler saat kabupaten dipilih - GENERATE KODE FAT
    kabupatenSelect.addEventListener('change', function() {
        const provinsi = provinsiSelect.value;
        const kabupaten = this.value;

        console.log('Kabupaten selected:', kabupaten, 'for provinsi:', provinsi);

        if (provinsi && kabupaten) {
            const url = `/report/operational/get-kode-fat?provinsi=${encodeURIComponent(provinsi)}&kabupaten=${encodeURIComponent(kabupaten)}`;
            console.log('Fetching FAT code URL:', url);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('FAT code response:', data);

                    if (data.kode_fat) {
                        kodeFatInput.value = data.kode_fat;
                        kodeFatInput.classList.add('fat-updated');
                        showNotification(`Kode FAT berhasil dibuat: ${data.kode_fat}`, 'success');

                        // Remove animation class after animation
                        setTimeout(() => {
                            kodeFatInput.classList.remove('fat-updated');
                        }, 1200);
                    } else {
                        kodeFatInput.value = '';
                        showNotification('Tidak dapat membuat kode FAT', 'warning');
                    }
                })
                .catch(error => {
                    console.error('FAT code fetch error:', error);
                    showNotification(`Gagal membuat kode FAT: ${error.message}`, 'error');
                });
        }
    });

    // Initialize: Load kabupaten if provinsi has value
    if (oldProvinsi) {
        console.log('Initializing with provinsi:', oldProvinsi);
        // Trigger the change event programmatically
        const event = new Event('change');
        provinsiSelect.dispatchEvent(event);
    }

    // ⭐ FUNCTIONALITY BUTTON AMBIL LOKASI SAAT INI - DITAMBAHKAN DI SINI ⭐
    const getCurrentLocationBtn = document.getElementById('getCurrentLocation');
    const locationStatus = document.getElementById('locationStatus');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');

    if (getCurrentLocationBtn) {
        getCurrentLocationBtn.addEventListener('click', function() {
            // Check if browser supports geolocation
            if (!navigator.geolocation) {
                locationStatus.textContent = '❌ Browser tidak mendukung geolocation';
                locationStatus.style.color = '#dc3545';
                showNotification('Browser Anda tidak mendukung fitur geolocation', 'error');
                return;
            }

            // Disable button and show loading state
            getCurrentLocationBtn.disabled = true;
            getCurrentLocationBtn.classList.add('loading');
            getCurrentLocationBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengambil lokasi...';
            locationStatus.textContent = '📍 Sedang mengambil koordinat GPS...';
            locationStatus.style.color = '#0dcaf0';

            // Get current position
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Success - update coordinates
                    const lat = position.coords.latitude.toFixed(7);
                    const lng = position.coords.longitude.toFixed(7);

                    latitudeInput.value = lat;
                    longitudeInput.value = lng;

                    // Add visual feedback
                    latitudeInput.classList.add('coordinate-updated');
                    longitudeInput.classList.add('coordinate-updated');

                    setTimeout(() => {
                        latitudeInput.classList.remove('coordinate-updated');
                        longitudeInput.classList.remove('coordinate-updated');
                    }, 800);

                    // Update status
                    locationStatus.textContent = `✅ Lokasi berhasil diambil! (Akurasi: ${position.coords.accuracy.toFixed(0)}m)`;
                    locationStatus.style.color = '#28a745';

                    // Reset button
                    getCurrentLocationBtn.disabled = false;
                    getCurrentLocationBtn.classList.remove('loading');
                    getCurrentLocationBtn.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i> Ambil Lokasi Saat Ini';

                    // Show success notification
                    showNotification(`Koordinat berhasil diambil: ${lat}, ${lng}`, 'success');

                    console.log('Location acquired:', {
                        latitude: lat,
                        longitude: lng,
                        accuracy: position.coords.accuracy
                    });
                },
                function(error) {
                    // Error handling
                    let errorMsg = '';
                    let errorDetail = '';

                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = '❌ Izin lokasi ditolak';
                            errorDetail = 'Silakan izinkan akses lokasi di browser Anda';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = '❌ Lokasi tidak tersedia';
                            errorDetail = 'Informasi lokasi tidak dapat diakses';
                            break;
                        case error.TIMEOUT:
                            errorMsg = '❌ Request timeout';
                            errorDetail = 'Waktu permintaan lokasi habis';
                            break;
                        default:
                            errorMsg = '❌ Error tidak diketahui';
                            errorDetail = 'Terjadi kesalahan saat mengambil lokasi';
                    }

                    locationStatus.textContent = `${errorMsg} - ${errorDetail}`;
                    locationStatus.style.color = '#dc3545';

                    // Reset button
                    getCurrentLocationBtn.disabled = false;
                    getCurrentLocationBtn.classList.remove('loading');
                    getCurrentLocationBtn.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i> Ambil Lokasi Saat Ini';

                    // Show error notification
                    showNotification(errorMsg + ': ' + errorDetail, 'error');

                    console.error('Geolocation error:', error);
                },
                {
                    enableHighAccuracy: true,  // Request high accuracy
                    timeout: 10000,            // 10 second timeout
                    maximumAge: 0              // Don't use cached position
                }
            );
        });
    }
});

// Function untuk menampilkan notifikasi
function showNotification(message, type = 'info') {
    const existing = document.querySelectorAll('.temp-notification');
    existing.forEach(n => n.remove());

    const alertClass = type === 'success' ? 'alert-success' :
                       type === 'error' ? 'alert-danger' :
                       type === 'warning' ? 'alert-warning' :
                       'alert-info';
    const iconClass = type === 'success' ? 'fa-check-circle' :
                      type === 'error' ? 'fa-exclamation-triangle' :
                      type === 'warning' ? 'fa-exclamation-triangle' :
                      'fa-info-circle';

    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed temp-notification`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px; font-size: 0.9rem;';
    notification.innerHTML = `
        <i class="fas ${iconClass} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    document.body.appendChild(notification);
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection
