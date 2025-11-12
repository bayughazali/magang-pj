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
                    {{-- NAMA SALES OTOMATIS --}}
                    <div class="col-md-3">
                        <label class="form-label">Nama Sales</label>
                        <input type="text" 
                               class="form-control bg-light fw-bold" 
                               value="{{ auth()->user()->name }}" 
                               readonly 
                               style="cursor: not-allowed;">
                        {{-- Hidden input untuk dikirim ke backend --}}
                        <input type="hidden" name="sales_name" value="{{ auth()->user()->name }}">
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Sales otomatis dari akun Anda
                        </small>
                    </div>

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
                            <th>Sales</th>
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
                                <td>
                                    @if($p->sales_name)
                                        <i class="fas fa-user-circle text-primary"></i>
                                        <strong>{{ $p->sales_name }}</strong>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
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
                                <td colspan="{{ auth()->user()->role === 'admin' ? '13' : '12' }}" class="text-center text-muted py-4">
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

.form-control[readonly] {
    background-color: #e9ecef;
    cursor: not-allowed;
    font-weight: 600;
}
</style>

{{-- LEAFLET CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

{{-- LEAFLET JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

{{-- JAVASCRIPT LENGKAP --}}
<script>
console.log('🚀 Script cascade dropdown dimuat...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM Content Loaded');

    // ========================================
    // 🗺️ INISIALISASI PETA LEAFLET
    // ========================================
    const map = L.map('map').setView([-8.409518, 115.188916], 10);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    let marker = L.marker([-8.409518, 115.188916], { 
        draggable: true,
        icon: L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(map);

    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateCoordinates(position.lat, position.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        document.getElementById('display-lat').textContent = lat.toFixed(6);
        document.getElementById('display-lng').textContent = lng.toFixed(6);
    }

    window.focusRegion = function(region) {
        const coordinates = {
            'bali': [-8.4095, 115.1889, 10],
            'ntb': [-8.6529, 116.3249, 9],
            'ntt': [-8.6574, 121.0794, 8]
        };
        
        if (coordinates[region]) {
            const [lat, lng, zoom] = coordinates[region];
            map.setView([lat, lng], zoom);
            marker.setLatLng([lat, lng]);
            updateCoordinates(lat, lng);
        }
    };

    // ========================================
    // 🔄 CASCADE DROPDOWN
    // ========================================
    const provinsiSelect = document.getElementById('provinsi');
    const kabupatenSelect = document.getElementById('kabupaten');
    const kecamatanSelect = document.getElementById('kecamatan');
    const kodeFatInput = document.getElementById('kode_fat');

    if (!provinsiSelect || !kabupatenSelect || !kecamatanSelect) {
        console.error('❌ Dropdown element tidak ditemukan!');
        return;
    }

    console.log('✅ Semua dropdown element ditemukan');

    // DATA REGION HARDCODED (BACKUP jika API gagal)
    const regionDataHardcoded = {
        'Bali': {
            'Badung': ['Kuta', 'Kuta Selatan', 'Kuta Utara', 'Mengwi', 'Abiansemal', 'Petang'],
            'Bangli': ['Bangli', 'Susut', 'Tembuku', 'Kintamani'],
            'Buleleng': ['Singaraja', 'Buleleng', 'Sukasada', 'Sawan', 'Kubutambahan', 'Tejakula', 'Seririt', 'Busungbiu', 'Banjar'],
            'Denpasar': ['Denpasar Barat', 'Denpasar Timur', 'Denpasar Selatan', 'Denpasar Utara'],
            'Gianyar': ['Gianyar', 'Blahbatuh', 'Sukawati', 'Ubud', 'Tegallalang', 'Tampaksiring', 'Payangan'],
            'Jembrana': ['Negara', 'Mendoyo', 'Pekutatan', 'Melaya', 'Jembrana'],
            'Karangasem': ['Karangasem', 'Abang', 'Bebandem', 'Rendang', 'Sidemen', 'Manggis', 'Selat', 'Kubu'],
            'Klungkung': ['Semarapura', 'Banjarangkan', 'Klungkung', 'Dawan'],
            'Tabanan': ['Tabanan', 'Kediri', 'Marga', 'Selemadeg', 'Kerambitan', 'Penebel']
        },
        'Nusa Tenggara Barat': {
            'Bima': ['Bima', 'Palibelo', 'Donggo', 'Sanggar', 'Woha'],
            'Dompu': ['Dompu', 'Kempo', 'Hu\'u', 'Kilo', 'Woja'],
            'Lombok Barat': ['Gerung', 'Kediri', 'Narmada', 'Lingsar', 'Gunungsari', 'Labuapi', 'Lembar', 'Sekotong', 'Kuripan'],
            'Lombok Tengah': ['Praya', 'Pujut', 'Jonggat', 'Batukliang', 'Kopang', 'Janapria', 'Pringgarata', 'Praya Barat', 'Praya Timur'],
            'Lombok Timur': ['Selong', 'Masbagik', 'Aikmel', 'Pringgabaya', 'Labuhan Haji', 'Sakra', 'Terara', 'Montong Gading', 'Suwela'],
            'Lombok Utara': ['Tanjung', 'Gangga', 'Kayangan', 'Bayan', 'Pemenang'],
            'Mataram': ['Ampenan', 'Mataram', 'Cakranegara', 'Sekarbela', 'Sandubaya', 'Selaparang'],
            'Sumbawa': ['Sumbawa', 'Unter Iwes', 'Moyo Hilir', 'Moyo Hulu', 'Alas', 'Batu Lanteh'],
            'Sumbawa Barat': ['Taliwang', 'Jereweh', 'Sekongkang', 'Maluk', 'Brang Rea']
        },
        'Nusa Tenggara Timur': {
            'Alor': ['Kalabahi', 'Alor Barat Daya', 'Alor Barat Laut', 'Alor Selatan', 'Alor Timur'],
            'Belu': ['Atambua', 'Tasifeto Barat', 'Tasifeto Timur', 'Malaka Barat', 'Malaka Tengah'],
            'Ende': ['Ende', 'Ndona', 'Nangapanda', 'Detusoko', 'Maurole', 'Wolowaru'],
            'Flores Timur': ['Larantuka', 'Ile Mandiri', 'Tanjung Bunga', 'Solor Timur', 'Solor Barat', 'Adonara Timur'],
            'Kupang': ['Kupang Tengah', 'Kupang Barat', 'Kupang Timur', 'Amarasi', 'Nekamese', 'Sulamu', 'Amfoang Selatan', 'Amfoang Utara']
        }
    };

    // HANDLER PROVINSI CHANGE
    provinsiSelect.addEventListener('change', function() {
        const provinsi = this.value;
        console.log('🔹 Provinsi dipilih:', provinsi);
        
        kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kodeFatInput.value = '';
        
        if (!provinsi) {
            kabupatenSelect.disabled = true;
            kecamatanSelect.disabled = true;
            return;
        }

        // GUNAKAN DATA HARDCODED LANGSUNG (lebih reliable)
        if (regionDataHardcoded[provinsi]) {
            console.log('✅ Menggunakan data hardcoded');
            const kabupatenList = Object.keys(regionDataHardcoded[provinsi]);
            
            kabupatenSelect.disabled = false;
            kabupatenList.forEach(kab => {
                const option = document.createElement('option');
                option.value = kab;
                option.textContent = kab;
                kabupatenSelect.appendChild(option);
            });
            
            console.log('✅ Kabupaten dimuat:', kabupatenList.length, 'items');
        } else {
            console.error('❌ Provinsi tidak ditemukan di data hardcoded');
            alert('Data provinsi tidak ditemukan');
        }
    });

    // HANDLER KABUPATEN CHANGE
    kabupatenSelect.addEventListener('change', function() {
        const provinsi = provinsiSelect.value;
        const kabupaten = this.value;
        console.log('🔹 Kabupaten dipilih:', kabupaten);
        
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kodeFatInput.value = '';
        
        if (!kabupaten) {
            kecamatanSelect.disabled = true;
            return;
        }

        // GUNAKAN DATA HARDCODED
        if (regionDataHardcoded[provinsi] && regionDataHardcoded[provinsi][kabupaten]) {
            const kecamatanList = regionDataHardcoded[provinsi][kabupaten];
            
            kecamatanSelect.disabled = false;
            kecamatanList.forEach(kec => {
                const option = document.createElement('option');
                option.value = kec;
                option.textContent = kec;
                kecamatanSelect.appendChild(option);
            });
            
            console.log('✅ Kecamatan dimuat:', kecamatanList.length, 'items');
        }

        // Generate FAT
        generateKodeFat(provinsi, kabupaten, '');
    });

    // HANDLER KECAMATAN CHANGE
    kecamatanSelect.addEventListener('change', function() {
        const provinsi = provinsiSelect.value;
        const kabupaten = kabupatenSelect.value;
        const kecamatan = this.value;
        
        if (kecamatan) {
            generateKodeFat(provinsi, kabupaten, kecamatan);
        }
    });

    // FUNCTION GENERATE KODE FAT
    function generateKodeFat(provinsi, kabupaten, kecamatan) {
        // Mapping kode FAT berdasarkan kabupaten (sesuai dengan controller)
        const fatMapping = {
            'Bali': {
                'Badung': 'FAT-BDG',
                'Bangli': 'FAT-BGL',
                'Buleleng': 'FAT-BLL',
                'Denpasar': 'FAT-DPS',
                'Gianyar': 'FAT-GNY',
                'Jembrana': 'FAT-JMB',
                'Karangasem': 'FAT-KAS',
                'Klungkung': 'FAT-KLK',
                'Tabanan': 'FAT-TBN'
            },
            'Nusa Tenggara Barat': {
                'Bima': 'FAT-BIM',
                'Dompu': 'FAT-DOM',
                'Lombok Barat': 'FAT-LBR',
                'Lombok Tengah': 'FAT-LTG',
                'Lombok Timur': 'FAT-LTM',
                'Lombok Utara': 'FAT-LUT',
                'Mataram': 'FAT-MTR',
                'Sumbawa': 'FAT-SBW',
                'Sumbawa Barat': 'FAT-SBR'
            },
            'Nusa Tenggara Timur': {
                'Alor': 'FAT-ALR',
                'Belu': 'FAT-BLU',
                'Ende': 'FAT-END',
                'Flores Timur': 'FAT-FLT',
                'Kupang': 'FAT-KPG'
            }
        };

        console.log('🔹 Generating FAT untuk:', {provinsi, kabupaten, kecamatan});

        // Cek apakah mapping ada
        if (!fatMapping[provinsi] || !fatMapping[provinsi][kabupaten]) {
            console.error('❌ Mapping FAT tidak ditemukan untuk:', provinsi, kabupaten);
            return;
        }

        const baseFat = fatMapping[provinsi][kabupaten];
        
        // Jika ada kecamatan, tambahkan kode kecamatan
        if (kecamatan) {
            const kecamatanCode = kecamatan.substring(0, 3).toUpperCase();
            
            // Hitung jumlah pelanggan yang sudah ada (simulasi - bisa diganti dengan fetch real count)
            // Format: FAT-BDG-KUT-001
            const kodeFat = `${baseFat}-${kecamatanCode}-001`;
            
            kodeFatInput.value = kodeFat;
            kodeFatInput.classList.add('fat-updated');
            setTimeout(() => {
                kodeFatInput.classList.remove('fat-updated');
            }, 1200);
            
            console.log('✅ Kode FAT generated:', kodeFat);
        } else {
            // Jika belum ada kecamatan, tampilkan base FAT
            const kodeFat = `${baseFat}-001`;
            kodeFatInput.value = kodeFat;
            console.log('✅ Kode FAT base generated:', kodeFat);
        }

        // OPTIONAL: Fetch dari server untuk mendapatkan nomor urut yang akurat
        const url = `/api/get-kode-fat?provinsi=${encodeURIComponent(provinsi)}&kabupaten=${encodeURIComponent(kabupaten)}&kecamatan=${encodeURIComponent(kecamatan)}`;
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    console.warn('⚠️ Fetch FAT dari server gagal, menggunakan lokal');
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success && data.kode_fat) {
                    console.log('✅ FAT dari server:', data.kode_fat);
                    kodeFatInput.value = data.kode_fat;
                    kodeFatInput.classList.add('fat-updated');
                    setTimeout(() => {
                        kodeFatInput.classList.remove('fat-updated');
                    }, 1200);
                }
            })
            .catch(error => {
                console.warn('⚠️ Error fetching FAT dari server:', error);
                // Tetap gunakan kode FAT lokal yang sudah di-generate
            });
    }

    // AUTO-LOAD untuk old values
    const oldProvinsi = provinsiSelect.value;
    const oldKabupaten = "{{ old('kabupaten') }}";
    const oldKecamatan = "{{ old('kecamatan') }}";

    if (oldProvinsi) {
        console.log('🔄 Auto-loading old values...');
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
                }, 500);
            }
        }, 500);
    }

    console.log('✅ Script cascade dropdown selesai dimuat');
});
</script>
@endsection