@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Input Data Pelanggan</h4>
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

            {{-- FORM INPUT --}}
            <form action="{{ route('report.operational.store') }}" method="POST" class="mb-4">
                @csrf
               <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">ID Pelanggan</label>
                        <input type="text"
                            class="form-control bg-light fw-bold text-center"
                            value="{{ $nextId ?? '' }}"
                            readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama Pelanggan *</label>
                        <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bandwidth *</label>
                        <select name="bandwidth" class="form-control" required>
                            <option value="">-- Pilih Kecepatan --</option>
                            <option value="10 Mbps" {{ old('bandwidth') == '10 Mbps' ? 'selected' : '' }}>10 Mbps</option>
                            <option value="20 Mbps" {{ old('bandwidth') == '20 Mbps' ? 'selected' : '' }}>20 Mbps</option>
                            <option value="50 Mbps" {{ old('bandwidth') == '50 Mbps' ? 'selected' : '' }}>50 Mbps</option>
                            <option value="100 Mbps" {{ old('bandwidth') == '100 Mbps' ? 'selected' : '' }}>100 Mbps</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nomor Telepon *</label>
                        <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}" required>
                    </div>

                    {{-- FIELD PROVINSI --}}
                    <div class="col-md-3">
                        <label class="form-label">Provinsi *</label>
                        <select name="provinsi" id="provinsi" class="form-control" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach ($regionData as $provinsi => $kabupatenList)
                                <option value="{{ $provinsi }}" {{ old('provinsi') == $provinsi ? 'selected' : '' }}>
                                    {{ $provinsi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FIELD KABUPATEN --}}
                    <div class="col-md-3">
                        <label class="form-label">Kabupaten/Kota *</label>
                        <select name="kabupaten" id="kabupaten" class="form-control" required disabled>
                            <option value="">-- Pilih Kabupaten --</option>
                        </select>
                    </div>

                    {{-- FIELD KECAMATAN --}}
                    <div class="col-md-3">
                        <label class="form-label">Kecamatan *</label>
                        <select name="kecamatan" id="kecamatan" class="form-control" required disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    {{-- FIELD FAT YANG SUDAH OTOMATIS --}}
                    <div class="col-md-3">
                        <label class="form-label">Kode FAT</label>
                        <input type="text" id="kode_fat" name="kode_fat" class="form-control fat-code-field" placeholder="Akan terisi otomatis..." value="{{ old('kode_fat') }}" readonly>
                        <small class="text-muted">Auto-generate setelah pilih wilayah</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Alamat *</label>
                        <textarea name="alamat" rows="2" class="form-control" required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control" placeholder="-8.409518" value="{{ old('latitude', '-8.409518') }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control" placeholder="115.188916" value="{{ old('longitude', '115.188916') }}" readonly>
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    </div>
                </div>
            </form>

           {{-- TABEL DATA PELANGGAN --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>ID Pelanggan</th>
                            <th>Nama</th>
                            <th>Bandwidth</th>
                            <th>Telepon</th>
                            <th>Provinsi</th>
                            <th>Kabupaten</th>
                            <th>Kecamatan</th>
                            <th>Alamat</th>
                            <th>Kode FAT</th>
                            <th>Koordinat</th>
                            @if(auth()->user()->role === 'admin')
                                <th style="width: 120px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelanggans as $index => $p)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><strong>{{ $p->id_pelanggan }}</strong></td>
                                <td>{{ $p->nama_pelanggan }}</td>
                                <td><span class="badge bg-info">{{ $p->bandwidth }}</span></td>
                                <td>{{ $p->nomor_telepon }}</td>
                                <td><span class="badge bg-primary">{{ $p->provinsi ?? '-' }}</span></td>
                                <td><span class="badge bg-secondary">{{ $p->kabupaten ?? '-' }}</span></td>
                                <td><span class="badge bg-success">{{ $p->kecamatan ?? '-' }}</span></td>
                                <td>{{ Str::limit($p->alamat, 30) }}</td>
                                <td><strong class="text-success">{{ $p->kode_fat ?: '-' }}</strong></td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $p->latitude }}, {{ $p->longitude }}
                                    </small>
                                </td>

                                @if(auth()->user()->role === 'admin')
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="{{ route('report.operational.edit', $p->id_pelanggan) }}"
                                            class="btn btn-warning btn-sm me-2"
                                            title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('pelanggan.destroy', $p->id_pelanggan) }}"
                                                method="POST"
                                                class="delete-form"
                                                onsubmit="return confirm('Yakin ingin menghapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'admin' ? '12' : '11' }}" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <br>Belum ada data pelanggan
                                    <br><small>Silakan input data pelanggan di form di atas</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(count($pelanggans) > 0)
                <div class="mt-3 text-end">
                    <small class="text-muted">Total: {{ count($pelanggans) }} pelanggan</small>
                </div>
            @endif
        </div>
    </div>

    {{-- Enhanced Map Section --}}
    <div class="mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt text-primary"></i> Pilih Lokasi Pelanggan</h5>
                <small class="text-muted">
                    <i class="fas fa-hand-pointer"></i> Klik pada peta atau seret marker untuk menentukan lokasi pelanggan
                </small>
            </div>
            <div class="card-body p-0" style="position: relative;">
                <div id="mapContainer" style="height:500px; width:100%; background: #f8f9fa; position: relative;">
                    <div id="map" style="height:100%; width:100%;"></div>
                </div>

                {{-- Coordinate Display Panel --}}
                <div class="coordinate-panel position-absolute" style="bottom: 25px; left: 25px; background: rgba(255,255,255,0.95); padding: 18px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); z-index: 1000; min-width: 250px;">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-crosshairs text-primary me-2"></i>
                        <strong>Koordinat Terpilih:</strong>
                    </div>
                    <div class="coordinate-info">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Latitude:</span>
                            <span id="display-lat" class="badge bg-primary">-8.409518</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Longitude:</span>
                            <span id="display-lng" class="badge bg-success">115.188916</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Location Buttons --}}
                <div class="position-absolute" style="top: 15px; right: 15px; z-index: 1000;">
                    <div class="btn-group-vertical" role="group">
                        <button type="button" class="btn btn-sm btn-primary region-btn mb-1" onclick="focusRegion('bali')">
                            <i class="fas fa-map-pin me-1"></i> Bali
                        </button>
                        <button type="button" class="btn btn-sm btn-info region-btn mb-1" onclick="focusRegion('ntb')">
                            <i class="fas fa-map-pin me-1"></i> NTB
                        </button>
                        <button type="button" class="btn btn-sm btn-warning region-btn" onclick="focusRegion('ntt')">
                            <i class="fas fa-map-pin me-1"></i> NTT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS --}}
<style>
.fat-code-field {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #28a745;
    font-weight: bold;
    color: #28a745;
    font-family: 'Courier New', monospace;
    text-align: center;
    letter-spacing: 1px;
}

.fat-updated {
    animation: fatUpdate 1.2s ease-in-out;
}

@keyframes fatUpdate {
    0%, 100% { transform: scale(1); background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
    50% { transform: scale(1.08); background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); box-shadow: 0 0 20px rgba(40, 167, 69, 0.6); }
}

.coordinate-panel .badge {
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    min-width: 80px;
}

.region-btn {
    transition: all 0.3s ease;
    padding: 8px 12px;
}

.region-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>

{{-- LEAFLET CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

{{-- JAVASCRIPT --}}
<script>
// ✅ Gunakan nama variabel unik untuk menghindari konflik dengan Argon template
let customerMap, customerMarker;
let mapInitialized = false;

const regions = {
    bali: { center: [-8.409518, 115.188916], zoom: 10 },
    ntb: { center: [-8.652894, 117.362238], zoom: 9 },
    ntt: { center: [-8.874650, 121.727200], zoom: 8 }
};

// CASCADE DROPDOWN
document.addEventListener('DOMContentLoaded', function() {
    const provinsiSelect = document.getElementById('provinsi');
    const kabupatenSelect = document.getElementById('kabupaten');
    const kecamatanSelect = document.getElementById('kecamatan');
    const kodeFatInput = document.getElementById('kode_fat');

    // Provinsi change
    provinsiSelect.addEventListener('change', function() {
        const provinsi = this.value;
        kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
        kabupatenSelect.disabled = true;
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanSelect.disabled = true;
        kodeFatInput.value = '';

        if (provinsi) {
            fetch(`/report/operational/get-kabupaten?provinsi=${encodeURIComponent(provinsi)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.kabupaten && data.kabupaten.length > 0) {
                        data.kabupaten.forEach(kab => {
                            const option = document.createElement('option');
                            option.value = kab;
                            option.textContent = kab;
                            kabupatenSelect.appendChild(option);
                        });
                        kabupatenSelect.disabled = false;
                        showNotification('Kabupaten berhasil dimuat!', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error loading kabupaten:', error);
                    showNotification('Gagal memuat data kabupaten', 'error');
                });
        }
    });

    // Kabupaten change - Load kecamatan
    kabupatenSelect.addEventListener('change', function() {
        const provinsi = provinsiSelect.value;
        const kabupaten = this.value;
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanSelect.disabled = true;
        kodeFatInput.value = '';

        if (provinsi && kabupaten) {
            console.log('📍 Loading kecamatan for:', { provinsi, kabupaten });
            
            const url = `/report/operational/get-kecamatan?provinsi=${encodeURIComponent(provinsi)}&kabupaten=${encodeURIComponent(kabupaten)}`;
            console.log('🔗 Request URL:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => {
                console.log('📥 Response status:', response.status);
                console.log('📥 Response headers:', response.headers.get('content-type'));
                
                // Cek apakah response adalah JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('❌ Response bukan JSON:', text);
                        throw new Error('Server mengembalikan HTML, bukan JSON. Cek route atau controller.');
                    });
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('✅ Kecamatan data:', data);
                
                if (data.success && data.kecamatan && Array.isArray(data.kecamatan) && data.kecamatan.length > 0) {
                    data.kecamatan.forEach(kec => {
                        const option = document.createElement('option');
                        option.value = kec;
                        option.textContent = kec;
                        kecamatanSelect.appendChild(option);
                    });
                    kecamatanSelect.disabled = false;
                    showNotification(`✅ ${data.kecamatan.length} kecamatan berhasil dimuat!`, 'success');
                } else {
                    console.warn('⚠️ Data kecamatan kosong atau invalid:', data);
                    showNotification('⚠️ Data kecamatan tidak ditemukan', 'error');
                }
            })
            .catch(error => {
                console.error('❌ Error loading kecamatan:', error);
                showNotification('❌ Gagal memuat kecamatan: ' + error.message, 'error');
            });
        }
    });

    // Kecamatan change - Generate FAT code
    // Kecamatan change - Generate FAT code
kecamatanSelect.addEventListener('change', function() {
    const provinsi = provinsiSelect.value;
    const kabupaten = kabupatenSelect.value;
    const kecamatan = this.value;

    // Reset kode FAT
    kodeFatInput.value = '';

    if (provinsi && kabupaten && kecamatan) {
        console.log('🔢 Generating FAT code for:', { provinsi, kabupaten, kecamatan });
        
        const url = `/report/operational/get-kode-fat?provinsi=${encodeURIComponent(provinsi)}&kabupaten=${encodeURIComponent(kabupaten)}&kecamatan=${encodeURIComponent(kecamatan)}`;
        console.log('🔗 FAT Request URL:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => {
            console.log('📥 FAT Response status:', response.status);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('❌ FAT Error response:', text);
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('✅ FAT Data:', data);
            
            if (data.success && data.kode_fat) {
                kodeFatInput.value = data.kode_fat;
                kodeFatInput.classList.add('fat-updated');
                showNotification(`✅ Kode FAT: ${data.kode_fat}`, 'success');
                
                setTimeout(() => {
                    kodeFatInput.classList.remove('fat-updated');
                }, 1200);
            } else {
                console.warn('⚠️ FAT generation failed:', data.error);
                showNotification('⚠️ Gagal generate kode FAT: ' + (data.error || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('❌ FAT Error:', error);
            showNotification('❌ Gagal generate kode FAT: ' + error.message, 'error');
        });
    }
});

    // Load old values
    const oldProvinsi = provinsiSelect.value;
    const oldKabupaten = '{{ old("kabupaten") }}';
    const oldKecamatan = '{{ old("kecamatan") }}';

    if (oldProvinsi) {
        provinsiSelect.dispatchEvent(new Event('change'));
        setTimeout(() => {
            if (oldKabupaten) {
                kabupatenSelect.value = oldKabupaten;
                kabupatenSelect.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    if (oldKecamatan) {
                        kecamatanSelect.value = oldKecamatan;
                        kecamatanSelect.dispatchEvent(new Event('change'));
                    }
                }, 1000);
            }
        }, 1000);
    }
});

// MAP INITIALIZATION
async function initializeMap() {
    if (mapInitialized) return;
    try {
        await new Promise((resolve) => {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = resolve;
            document.head.appendChild(script);
        });

        const defaultLocation = regions.bali.center;
        customerMap = L.map('map').setView(defaultLocation, regions.bali.zoom);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(customerMap);

        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<div style="background: linear-gradient(45deg, #007bff, #0056b3); width: 24px; height: 24px; border-radius: 50% 50% 50% 0; border: 3px solid white; box-shadow: 0 3px 10px rgba(0,0,0,0.4); transform: rotate(-45deg);"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 24]
        });

        customerMarker = L.marker(defaultLocation, { draggable: true, icon: customIcon }).addTo(customerMap);

        customerMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateCoordinates(pos.lat, pos.lng);
        });

        customerMap.on('click', function(e) {
            customerMarker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });

        updateCoordinates(defaultLocation[0], defaultLocation[1]);
        mapInitialized = true;
    } catch (error) {
        console.error('Map error:', error);
    }
}

function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);
    document.getElementById('display-lat').textContent = lat.toFixed(6);
    document.getElementById('display-lng').textContent = lng.toFixed(6);
}

function focusRegion(regionKey) {
    if (!customerMap || !mapInitialized) return;
    const region = regions[regionKey];
    if (region) {
        customerMap.flyTo(region.center, region.zoom);
        setTimeout(() => {
            customerMarker.setLatLng(region.center);
            updateCoordinates(region.center[0], region.center[1]);
        }, 1000);
    }
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
    notification.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Initialize map on load
window.addEventListener('load', () => setTimeout(initializeMap, 500));

// DELETE AJAX
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!confirm('Yakin ingin menghapus?')) return;

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endsection