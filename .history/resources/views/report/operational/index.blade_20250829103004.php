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
                        <label class="form-label">ID Pelanggan *</label>
                        <input type="text" name="id_pelanggan" class="form-control" value="{{ old('id_pelanggan') }}" required>
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

                    {{-- FIELD BARU: PROVINSI --}}
                    <div class="col-md-4">
                        <label class="form-label">Provinsi *</label>
                        <select name="provinsi" id="provinsi" class="form-control" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($regionData as $provinsi => $kabupaten)
                                <option value="{{ $provinsi }}" {{ old('provinsi') == $provinsi ? 'selected' : '' }}>{{ $provinsi }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FIELD BARU: KABUPATEN --}}
                    <div class="col-md-4">
                        <label class="form-label">Kabupaten/Kota *</label>
                        <select name="kabupaten" id="kabupaten" class="form-control" required>
                            <option value="">-- Pilih Kabupaten --</option>
                        </select>
                    </div>


                    {{-- FIELD FAT YANG SUDAH OTOMATIS --}}
                    <div class="col-md-4">
                        <label class="form-label">Kode FAT</label>
                        <input type="text" id="kode_fat" name="kode_fat" class="form-control fat-code-field" placeholder="Akan terisi otomatis..." value="{{ old('kode_fat') }}" readonly>
                        <small class="text-muted">Kode FAT akan muncul setelah memilih provinsi dan kabupaten</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Alamat *</label>
                        <textarea name="alamat" rows="2" class="form-control" required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cluster *</label>
                        <select name="cluster" class="form-control" required>
                            <option value="">-- Pilih Cluster --</option>
                            <option value="Cluster A" {{ old('cluster') == 'Cluster A' ? 'selected' : '' }}>Cluster A</option>
                            <option value="Cluster B" {{ old('cluster') == 'Cluster B' ? 'selected' : '' }}>Cluster B</option>
                            <option value="Cluster C" {{ old('cluster') == 'Cluster C' ? 'selected' : '' }}>Cluster C</option>
                            <option value="Cluster D" {{ old('cluster') == 'Cluster D' ? 'selected' : '' }}>Cluster D</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control" placeholder="-8.409518" value="{{ old('latitude', '-8.409518') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control" placeholder="115.188916" value="{{ old('longitude', '115.188916') }}" readonly>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    </div>
                </div>
            </form>

            {{-- TABEL DATA PELANGGAN --}}
            <div class="mt-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-table"></i> Data Pelanggan Tersimpan</h5>
                        <small class="text-white-50">Daftar pelanggan yang telah diinput ke sistem</small>
                    </div>
                    <div class="card-body">
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
                                        <th>Alamat</th>
                                        <th>Cluster</th>
                                        <th>Kode FAT</th>
                                        <th>Koordinat</th>
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
                                            <td>{{ Str::limit($p->alamat, 30) }}</td>
                                            <td><span class="badge bg-warning text-dark">{{ $p->cluster }}</span></td>
                                            <td><strong class="text-success">{{ $p->kode_fat ?: '-' }}</strong></td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    {{ $p->latitude }}, {{ $p->longitude }}
                                                </small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-4">
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
            </div>

            {{-- Enhanced Map Section --}}
            <div class="mt-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt text-primary"></i> Pilih Lokasi Pelanggan</h5>
                        <small class="text-muted">
                            <i class="fas fa-hand-pointer"></i> Klik pada peta atau seret marker untuk menentukan lokasi pelanggan
                            <br><i class="fas fa-sync-alt"></i> Koordinat akan otomatis terupdate saat marker dipindah
                        </small>
                    </div>
                    <div class="card-body p-0" style="position: relative;">

                        {{-- Map Container --}}
                        <div id="mapContainer" style="height:500px; width:100%; background: #f8f9fa; position: relative;">
                            {{-- Actual Map --}}
                            <div id="map" style="height:100%; width:100%;"></div>
                        </div>

                        {{-- Coordinate Display Panel --}}
                        <div class="coordinate-panel position-absolute" style="bottom: 25px; left: 25px; background: rgba(255,255,255,0.95); padding: 18px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); backdrop-filter: blur(8px); z-index: 1000; border: 2px solid rgba(0,123,255,0.2); min-width: 250px;">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-crosshairs text-primary me-2" style="font-size: 1.1rem;"></i>
                                <strong style="font-size: 1rem; color: #2c3e50;">Koordinat Terpilih:</strong>
                            </div>
                            <div class="coordinate-info">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted" style="font-weight: 500;">Latitude:</span>
                                    <span id="display-lat" class="badge bg-primary coordinate-value" style="font-size: 0.85rem; padding: 6px 10px;">-8.409518</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted" style="font-weight: 500;">Longitude:</span>
                                    <span id="display-lng" class="badge bg-success coordinate-value" style="font-size: 0.85rem; padding: 6px 10px;">115.188916</span>
                                </div>
                            </div>
                            <div class="border-top pt-3 mt-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marked-alt text-info me-1"></i>
                                        <span id="region-info" style="font-weight: 500; color: #495057;">Bali</span>
                                    </small>
                                    <small class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Siap disimpan
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Enhanced Quick Location Buttons --}}
                        <div class="position-absolute" style="top: 15px; right: 15px; z-index: 1000;">
                            <div class="btn-group-vertical" role="group">
                                <button type="button" class="btn btn-sm btn-primary region-btn" onclick="focusRegion('bali')" title="Fokus ke Bali" style="margin-bottom: 3px; padding: 8px 12px;">
                                    <i class="fas fa-map-pin me-1"></i> Bali
                                </button>
                                <button type="button" class="btn btn-sm btn-info region-btn" onclick="focusRegion('ntb')" title="Fokus ke NTB" style="margin-bottom: 3px; padding: 8px 12px;">
                                    <i class="fas fa-map-pin me-1"></i> NTB
                                </button>
                                <button type="button" class="btn btn-sm btn-warning region-btn" onclick="focusRegion('ntt')" title="Fokus ke NTT" style="padding: 8px 12px;">
                                    <i class="fas fa-map-pin me-1"></i> NTT
                                </button>
                            </div>
                        </div>

                        {{-- Enhanced Status Indicator --}}
                        <div class="position-absolute" style="top: 15px; left: 15px; z-index: 1000;">
                            <div id="map-status" class="badge bg-success" style="padding: 8px 12px; font-size: 0.85rem;">
                                <i class="fas fa-check-circle me-1"></i> Peta Aktif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
.coordinate-panel {
    min-width: 220px;
    font-size: 0.9rem;
}

.coordinate-panel .badge {
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    min-width: 80px;
    text-align: center;
}

.coordinate-panel .coordinate-value {
    transition: all 0.3s ease;
    animation: pulse-subtle 2s infinite;
}

@keyframes pulse-subtle {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.coordinate-panel:hover .coordinate-value {
    animation: none;
    transform: scale(1.05);
}

.region-btn {
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255,255,255,0.3);
}

.region-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

#map-status {
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

/* Enhanced marker styling */
.custom-marker {
    background: linear-gradient(45deg, #007bff, #0056b3);
    width: 24px;
    height: 24px;
    border-radius: 50% 50% 50% 0;
    border: 3px solid white;
    box-shadow: 0 3px 10px rgba(0,0,0,0.4);
    cursor: grab;
    transition: all 0.2s ease;
    transform: rotate(-45deg);
    animation: marker-bounce 2s infinite;
}

.custom-marker:hover {
    transform: rotate(-45deg) scale(1.1);
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}

.custom-marker:active {
    cursor: grabbing;
}

@keyframes marker-bounce {
    0%, 20%, 50%, 80%, 100% { transform: rotate(-45deg) translateY(0); }
    40% { transform: rotate(-45deg) translateY(-3px); }
    60% { transform: rotate(-45deg) translateY(-2px); }
}

/* Enhanced coordinate update animation */
.coordinate-updated {
    animation: coordinate-pulse 1s ease-in-out;
}

@keyframes coordinate-pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); box-shadow: 0 0 10px rgba(40, 167, 69, 0.6); }
    100% { transform: scale(1); }
}

.region-btn.active {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    border-color: rgba(255,255,255,0.6);
}

/* Styling khusus untuk field kode FAT */
.fat-code-field {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #28a745;
    font-weight: bold;
    color: #28a745;
    font-family: 'Courier New', monospace;
    text-align: center;
    letter-spacing: 1px;
}

.fat-code-field:focus {
    box-shadow: 0 0 15px rgba(40, 167, 69, 0.4);
    border-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
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
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        box-shadow: 0 0 20px rgba(40, 167, 69, 0.6);
    }
    50% {
        transform: scale(1.08);
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border-color: #17a2b8;
        color: #17a2b8;
    }
    75% {
        transform: scale(1.05);
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-color: #28a745;
        color: #28a745;
    }
    100% {
        transform: scale(1);
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
}

/* Leaflet Map Styles */
.leaflet-container {
    font-family: inherit;
    border-radius: 0;
}

@media (max-width: 768px) {
    .coordinate-panel {
        position: relative !important;
        bottom: auto !important;
        left: auto !important;
        margin: 15px;
        width: calc(100% - 30px);
    }

    .position-absolute[style*="top: 15px"] {
        position: relative !important;
        top: auto !important;
        right: auto !important;
        margin: 10px;
        text-align: center;
    }

    .btn-group-vertical {
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 5px;
    }
}
</style>

{{-- LEAFLET CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>

      <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

      <script>
// ====== DOM Elements ======
const provinsiSelect = document.getElementById('provinsi');
const kabupatenSelect = document.getElementById('kabupaten');
const kodeFatInput = document.getElementById('kode_fat');
const koordinatInput = document.getElementById('koordinat');
const mapElement = document.getElementById('map');
const mapStatus = document.getElementById('map-status');

// ====== Initialize Map ======
const map = L.map(mapElement).setView([-2.5489, 118.0149], 5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

const marker = L.marker([-2.5489, 118.0149], { draggable: true }).addTo(map);

// ====== Helper Functions ======
function updateMapStatus(message, type = 'info') {
  const icons = {
    info: 'fas fa-info-circle text-blue-500',
    success: 'fas fa-check-circle text-green-500',
    error: 'fas fa-exclamation-circle text-red-500'
  };
  mapStatus.innerHTML = `<i class="${icons[type] || icons.info}"></i> ${message}`;
}

function showNotification(message, type = 'success') {
  const bg = type === 'error' ? 'bg-red-500' : 'bg-green-500';
  const notification = document.createElement('div');
  notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white ${bg} shadow-lg z-50`;
  notification.textContent = message;
  document.body.appendChild(notification);
  setTimeout(() => notification.remove(), 3000);
}

function formatCoordinates(lat, lng) {
  return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
}

// ====== Event Listeners ======

// Provinsi -> fetch Kabupaten
provinsiSelect.addEventListener('change', async function() {
  const provinsi = this.value;
  kabupatenSelect.innerHTML = '<option value="">Loading...</option>';
  try {
    const url = `/report/operational/get-kabupaten?provinsi=${encodeURIComponent(provinsi)}`;
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
    data.forEach(kab => {
      const option = document.createElement('option');
      option.value = kab.nama_kabupaten;
      option.textContent = kab.nama_kabupaten;
      kabupatenSelect.appendChild(option);
    });
  } catch (error) {
    console.error('Error:', error);
    showNotification(`Gagal memuat data kabupaten: ${error.message}`, 'error');
  }
});

// Kabupaten -> fetch kode FAT
kabupatenSelect.addEventListener('change', async function() {
  const provinsi = provinsiSelect.value;
  const kabupaten = this.value;
  try {
    const url = `/report/operational/get-kode-fat?provinsi=${encodeURIComponent(provinsi)}&kabupaten=${encodeURIComponent(kabupaten)}`;
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    kodeFatInput.value = data.kode_fat;
    showNotification(`Kode FAT berhasil dibuat: ${data.kode_fat}`, 'success');
  } catch (error) {
    console.error('Error:', error);
    showNotification(`Gagal membuat kode FAT: ${error.message}`, 'error');
  }
});

// Marker Drag -> update koordinat
marker.on('dragend', function(e) {
  const latlng = marker.getLatLng();
  const formattedLat = latlng.lat.toFixed(6);
  const formattedLng = latlng.lng.toFixed(6);
  const formatted = formatCoordinates(latlng.lat, latlng.lng);
  koordinatInput.value = formatted;
  console.log(`Coordinates updated: ${formattedLat}, ${formattedLng}`);
  updateMapStatus(`Koordinat diperbarui: ${formatted}`, 'success');
});

// Pindah map berdasarkan provinsi / kabupaten
async function focusRegion(regionName) {
  if (!regionName) return;
  try {
    updateMapStatus(`Berpindah ke ${regionName}...`, 'info');
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(regionName)}`;
    const response = await fetch(url);
    const data = await response.json();
    if (data.length > 0) {
      const { lat, lon } = data[0];
      const latNum = parseFloat(lat);
      const lonNum = parseFloat(lon);
      map.setView([latNum, lonNum], 11);
      marker.setLatLng([latNum, lonNum]);
      const formatted = formatCoordinates(latNum, lonNum);
      koordinatInput.value = formatted;
      updateMapStatus(`Lokasi: ${regionName}`, 'success');
      showNotification(`Lokasi dipindahkan ke ${regionName}`, 'success');
    }
  } catch (error) {
    console.error('Error focusing region:', error);
    updateMapStatus(`Gagal memindahkan ke ${regionName}`, 'error');
  }
}

provinsiSelect.addEventListener('change', () => focusRegion(provinsiSelect.value));
kabupatenSelect.addEventListener('change', () => focusRegion(kabupatenSelect.value));

</script>
@endsection
