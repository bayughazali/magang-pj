<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Competitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OperationalReportController extends Controller
{

    // ✳ : generator ID harian: IP + yymmdd + 2 digit urut per hari
    private function generateNextCustomerId(): string
    {
        $today = Carbon::now('Asia/Makassar')->format('ymd'); // 250904
        $prefix = 'IP' . $today;                              // IP250904

        // Ambil ID terakhir untuk hari ini lalu increment 2 digit di belakang
        $lastIdToday = Pelanggan::where('id_pelanggan', 'like', $prefix.'%')
            ->orderBy('id_pelanggan', 'desc')
            ->value('id_pelanggan');

        $seq = 0;
        if ($lastIdToday) {
            // Ambil 2 digit paling belakang sebagai urutan
            $seq = (int)substr($lastIdToday, strlen($prefix)); // contoh '01' -> 1
        }

        $nextSeq = str_pad((string)($seq + 1), 2, '0', STR_PAD_LEFT); // 01, 02, ...
        return $prefix . $nextSeq; // contoh: IP25090401
    }

    // Data provinsi dan kabupaten Indonesia Timur
    private function getRegionData()
    {
        return [
            'Bali' => [
                'Badung' => 'FAT-BDG',
                'Bangli' => 'FAT-BGL',
                'Buleleng' => 'FAT-BLL',
                'Denpasar' => 'FAT-DPS',
                'Gianyar' => 'FAT-GNY',
                'Jembrana' => 'FAT-JMB',
                'Karangasem' => 'FAT-KAS',
                'Klungkung' => 'FAT-KLK',
                'Tabanan' => 'FAT-TBN'
            ],
            'Nusa Tenggara Barat' => [
                'Bima' => 'FAT-BIM',
                'Dompu' => 'FAT-DOM',
                'Lombok Barat' => 'FAT-LBR',
                'Lombok Tengah' => 'FAT-LTG',
                'Lombok Timur' => 'FAT-LTM',
                'Lombok Utara' => 'FAT-LUT',
                'Mataram' => 'FAT-MTR',
                'Sumbawa' => 'FAT-SBW',
                'Sumbawa Barat' => 'FAT-SBR'
            ],
            'Nusa Tenggara Timur' => [
                'Alor' => 'FAT-ALR',
                'Belu' => 'FAT-BLU',
                'Ende' => 'FAT-END',
                'Flores Timur' => 'FAT-FLT',
                'Kupang' => 'FAT-KPG',
                'Lembata' => 'FAT-LMB',
                'Malaka' => 'FAT-MLK',
                'Manggarai' => 'FAT-MGG',
                'Manggarai Barat' => 'FAT-MGB',
                'Manggarai Timur' => 'FAT-MGT',
                'Nagekeo' => 'FAT-NGK',
                'Ngada' => 'FAT-NGD',
                'Rote Ndao' => 'FAT-RTN',
                'Sabu Raijua' => 'FAT-SBR',
                'Sikka' => 'FAT-SKK',
                'Sumba Barat' => 'FAT-SBT',
                'Sumba Barat Daya' => 'FAT-SBD',
                'Sumba Tengah' => 'FAT-STG',
                'Sumba Timur' => 'FAT-STM',
                'Timor Tengah Selatan' => 'FAT-TTS',
                'Timor Tengah Utara' => 'FAT-TTU'
            ]
        ];
    }

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
                    value="{{ $nextId }}"
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
                        <select name="kabupaten" id="kabupaten" class="form-control" required disabled>
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

<script>
// Global variables
let map, marker;
let mapInitialized = false;

// Region configuration
const regions = {
    bali: {
        center: [-8.409518, 115.188916],
        zoom: 10,
        name: 'Bali'
    },
    ntb: {
        center: [-8.652894, 117.362238],
        zoom: 9,
        name: 'Nusa Tenggara Barat'
    },
    ntt: {
        center: [-8.874650, 121.727200],
        zoom: 8,
        name: 'Nusa Tenggara Timur'
    }
};

// JAVASCRIPT UNTUK AUTO DROPDOWN DAN FAT - FIXED VERSION
document.addEventListener('DOMContentLoaded', function() {
    const provinsiSelect = document.getElementById('provinsi');
    const kabupatenSelect = document.getElementById('kabupaten');
    const kodeFatInput = document.getElementById('kode_fat');

    // Event handler saat provinsi dipilih
    provinsiSelect.addEventListener('change', function() {
        const provinsi = this.value;

        console.log('Provinsi selected:', provinsi); // Debug log

        // Reset kabupaten dan kode FAT
        kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
        kabupatenSelect.disabled = true;
        kodeFatInput.value = '';

        if (provinsi) {
            const url = `/report/operational/get-kabupaten?provinsi=${encodeURIComponent(provinsi)}`;
            console.log('Fetching kabupaten URL:', url); // Debug log

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Kabupaten response:', data); // Debug log

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

        console.log('Kabupaten selected:', kabupaten, 'for provinsi:', provinsi); // Debug log

        if (provinsi && kabupaten) {
            const url = `/report/operational/get-kode-fat?provinsi=${encodeURIComponent(provinsi)}&kabupaten=${encodeURIComponent(kabupaten)}`;
            console.log('Fetching FAT code URL:', url); // Debug log

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('FAT code response:', data); // Debug log

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

    // Load kabupaten jika ada old value (untuk form validation error)
    const oldProvinsi = provinsiSelect.value;
    const oldKabupaten = '{{ old("kabupaten") }}';

    console.log('Old values - Provinsi:', oldProvinsi, 'Kabupaten:', oldKabupaten); // Debug log

    if (oldProvinsi) {
        // Trigger the change event programmatically
        const event = new Event('change');
        provinsiSelect.dispatchEvent(event);

        // Wait for the kabupaten to load, then set the old value
        setTimeout(() => {
            if (oldKabupaten) {
                kabupatenSelect.value = oldKabupaten;
                // Trigger kabupaten change to generate FAT code
                const kabEvent = new Event('change');
                kabupatenSelect.dispatchEvent(kabEvent);
            }
        }, 1000);
    }
});

// Check if Leaflet is loaded
function checkLeafletLoad() {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        const maxAttempts = 10;

        const check = () => {
            attempts++;

            if (typeof L !== 'undefined' && L.map) {
                console.log('Leaflet loaded successfully');
                resolve(true);
            } else if (attempts >= maxAttempts) {
                console.error('Leaflet failed to load after', maxAttempts, 'attempts');
                reject(new Error('Leaflet tidak dapat dimuat'));
            } else {
                console.log('Checking Leaflet...', attempts);
                setTimeout(check, 500);
            }
        };

        check();
    });
}

async function initializeMap() {
    if (mapInitialized) return;

    try {
        console.log('Starting map initialization...');

        // Wait for Leaflet to load
        await checkLeafletLoad();

        // Default location (Bali)
        const defaultLocation = regions.bali.center;

        // Initialize Leaflet map immediately
        map = L.map('map', {
            center: defaultLocation,
            zoom: regions.bali.zoom,
            zoomControl: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            dragging: true,
            attributionControl: false
        });

        // Add OpenStreetMap tiles with error handling
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
            minZoom: 5,
            errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        });

        tileLayer.on('tileerror', function(error) {
            console.warn('Tile loading error:', error);
        });

        tileLayer.addTo(map);

        // Create enhanced custom marker
        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<div class="custom-marker"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 24]
        });

        // Add draggable marker with enhanced interaction
        marker = L.marker(defaultLocation, {
            draggable: true,
            icon: customIcon,
            riseOnHover: true
        }).addTo(map);

        // Enhanced marker tooltip
        marker.bindTooltip('Seret untuk mengubah lokasi', {
            permanent: false,
            direction: 'top',
            offset: [0, -30]
        });

        // Real-time coordinate update events
        marker.on('dragstart', function(e) {
            updateMapStatus('Mengubah koordinat...', 'warning');
            const tooltip = e.target.getTooltip();
            if (tooltip) tooltip.setContent('Mengubah lokasi...');
        });

        marker.on('drag', function(e) {
            const pos = e.target.getLatLng();
            // Update coordinates in real-time during drag
            updateCoordinatesRealTime(pos.lat, pos.lng);
        });

        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateCoordinates(pos.lat, pos.lng);
            updateMapStatus('Koordinat berhasil diubah', 'success');

            // Show success notification
            showNotification(`Koordinat diubah ke: ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`, 'success');

            // Reset tooltip
            const tooltip = e.target.getTooltip();
            if (tooltip) tooltip.setContent('Seret untuk mengubah lokasi');

            // Add pulse effect to coordinate panel
            const panel = document.querySelector('.coordinate-panel');
            if (panel) {
                panel.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    panel.style.transform = 'scale(1)';
                }, 200);
            }
        });

        // Enhanced map click event
        map.on('click', function(e) {
            const pos = e.latlng;
            marker.setLatLng(pos);
            updateCoordinates(pos.lat, pos.lng);
            updateMapStatus('Lokasi dipindah via klik', 'info');
            showNotification('Marker dipindah ke lokasi yang diklik', 'info');

            // Smooth animation
            map.flyTo(pos, map.getZoom(), {
                animate: true,
                duration: 0.5
            });
        });

        // Set boundaries for Indonesia Timur
        const bounds = L.latLngBounds([[-11.5, 113.0], [-7.0, 125.0]]);
        map.setMaxBounds(bounds);

        map.on('drag', function() {
            map.panInsideBounds(bounds, { animate: false });
        });

        // Initial coordinate update
        updateCoordinates(defaultLocation[0], defaultLocation[1]);

        // Force map to invalidate size after initialization
        setTimeout(() => {
            if (map) {
                map.invalidateSize();
                console.log('Map size invalidated');
            }
        }, 100);

        mapInitialized = true;
        console.log('Map initialized successfully!');
        showNotification('Peta berhasil dimuat!', 'success');

    } catch (error) {
        console.error('Map initialization error:', error);
        showMapError('Terjadi kesalahan saat memuat peta. Silakan refresh halaman.');
    }
}

function updateCoordinatesRealTime(lat, lng) {
    const formattedLat = lat.toFixed(6);
    const formattedLng = lng.toFixed(6);

    // Update display panel only (not form inputs during drag for performance)
    const displayLat = document.getElementById('display-lat');
    const displayLng = document.getElementById('display-lng');

    if (displayLat) {
        displayLat.textContent = formattedLat;
        displayLat.style.backgroundColor = '#ffc107'; // Warning color during drag
    }
    if (displayLng) {
        displayLng.textContent = formattedLng;
        displayLng.style.backgroundColor = '#ffc107';
    }

    // Update region info
    const regionName = getRegionName(lat, lng);
    const regionInfo = document.getElementById('region-info');
    if (regionInfo) regionInfo.textContent = regionName;
}

function updateCoordinates(lat, lng) {
    const formattedLat = lat.toFixed(6);
    const formattedLng = lng.toFixed(6);

    // Update form inputs
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    if (latInput) latInput.value = formattedLat;
    if (lngInput) lngInput.value = formattedLng;

    // Update display panel with final colors
    const displayLat = document.getElementById('display-lat');
    const displayLng = document.getElementById('display-lng');

    if (displayLat) {
        displayLat.textContent = formattedLat;
        displayLat.style.backgroundColor = '#007bff'; // Back to primary color
        displayLat.classList.add('coordinate-updated');
    }
    if (displayLng) {
        displayLng.textContent = formattedLng;
        displayLng.style.backgroundColor = '#28a745'; // Back to success color
        displayLng.classList.add('coordinate-updated');
    }

    // Remove animation class after animation
    setTimeout(() => {
        if (displayLat) displayLat.classList.remove('coordinate-updated');
        if (displayLng) displayLng.classList.remove('coordinate-updated');
    }, 1000);

    // Update region info
    const regionName = getRegionName(lat, lng);
    const regionInfo = document.getElementById('region-info');
    if (regionInfo) regionInfo.textContent = regionName;

    // Log for debugging
    console.log(`Coordinates updated: ${formattedLat}, ${formattedLng} (${regionName})`);
}

function updateMapStatus(message, type = 'success') {
    const statusEl = document.getElementById('map-status');
    if (statusEl) {
        const colors = {
            success: 'bg-success',
            warning: 'bg-warning',
            info: 'bg-info',
            error: 'bg-danger'
        };

        const icons = {
            success: 'fas fa-check-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle',
            error: 'fas fa-times-circle'
        };

        // Remove all color classes
        Object.values(colors).forEach(cls => statusEl.classList.remove(cls));

        // Add new color
        statusEl.classList.add(colors[type] || colors.success);
        statusEl.innerHTML = `<i class="${icons[type] || icons.success}"></i> ${message}`;

        // Auto reset after 3 seconds
        setTimeout(() => {
            if (statusEl.classList.contains(colors[type])) {
                statusEl.classList.remove(colors[type]);
                statusEl.classList.add('bg-success');
                statusEl.innerHTML = '<i class="fas fa-check-circle"></i> Peta Aktif';
            }
        }, 3000);
    }
}

function getRegionName(lat, lng) {
    if (lng >= 114.0 && lng <= 116.5 && lat >= -9.0 && lat <= -8.0) {
        return 'Bali';
    } else if (lng >= 115.5 && lng <= 119.5 && lat >= -9.5 && lat <= -8.0) {
        return 'Nusa Tenggara Barat';
    } else if (lng >= 119.0 && lng <= 125.0 && lat >= -10.5 && lat <= -8.0) {
        return 'Nusa Tenggara Timur';
    } else {
        return 'Indonesia Timur';
    }
}

function focusRegion(regionKey) {
    if (!map || !marker || !mapInitialized) {
        console.warn('Map not initialized yet');
        return;
    }

    const region = regions[regionKey];
    if (region) {
        updateMapStatus(`Berpindah ke ${region.name}...`, 'info');

        // Smooth fly animation
        map.flyTo(region.center, region.zoom, {
            animate: true,
            duration: 2
        });

        // Move marker with delay for better UX
        setTimeout(() => {
            marker.setLatLng(region.center);
            updateCoordinates(region.center[0], region.center[1]);
            updateMapStatus(`Lokasi: ${region.name}`, 'success');
            showNotification(`Lokasi dipindahkan ke ${region.name}`, 'success');
        }, 1000);

        // Add visual feedback to clicked button
        const buttons = document.querySelectorAll('.region-btn');
        buttons.forEach(btn => btn.classList.remove('active'));

        const clickedBtn = document.querySelector(`[onclick="focusRegion('${regionKey}')"]`);
        if (clickedBtn) {
            clickedBtn.classList.add('active');
            setTimeout(() => clickedBtn.classList.remove('active'), 3000);
        }
    }
}

// Enhanced notification function with better error handling
function showNotification(message, type = 'info') {
    // Remove existing notifications
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

function showMapError(message) {
    const container = document.getElementById('mapContainer');
    if (container) {
        container.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h5>Peta Tidak Dapat Dimuat</h5>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary btn-sm" onclick="location.reload()">
                        <i class="fas fa-refresh me-1"></i> Muat Ulang
                    </button>
                </div>
            </div>
        `;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing map...');

    // Add Leaflet script dynamically
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
    script.crossOrigin = '';

    script.onload = function() {
        console.log('Leaflet script loaded');
        setTimeout(initializeMap, 100);
    };

    script.onerror = function() {
        console.error('Failed to load Leaflet script');
        showMapError('Gagal memuat library peta. Periksa koneksi internet Anda.');
    };

    document.head.appendChild(script);
});

// Additional fallback
window.addEventListener('load', function() {
    setTimeout(function() {
        if (!mapInitialized) {
            console.log('Fallback initialization...');
            initializeMap();
        }
    }, 2000);
});
</script>
@endsection

    // API untuk mendapatkan kabupaten berdasarkan provinsi - DENGAN DEBUGGING
    // ⬇ Tambahkan method create() di sini
    public function create()
    {
    $nextId = $this->generateNextCustomerId(); // ✅ langsung panggil fungsi generator

    // Ambil data tambahan untuk form (jika dibutuhkan)
    $regionData = $this->getRegionData();
    $pakets = Competitor::select('paket')->distinct()->pluck('paket');

    // arahkan ke view form create
    return view('report.operational.create', compact('nextId', 'regionData', 'pakets'));
}
    public function getKabupaten(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');

            // Log untuk debugging
            Log::info('Get Kabupaten Request:', [
                'provinsi' => $provinsi,
                'all_params' => $request->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

            // Validasi input
            if (empty($provinsi)) {
                Log::warning('Empty provinsi parameter');
                return response()->json([
                    'kabupaten' => [],
                    'error' => 'Provinsi parameter is required'
                ], 400);
            }

            $regionData = $this->getRegionData();

            // Log data region untuk debugging
            Log::info('Region Data Available:', [
                'provinces' => array_keys($regionData)
            ]);

            if (isset($regionData[$provinsi])) {
                $kabupaten = array_keys($regionData[$provinsi]);

                Log::info('Kabupaten Found:', [
                    'provinsi' => $provinsi,
                    'kabupaten_count' => count($kabupaten),
                    'kabupaten' => $kabupaten
                ]);

                return response()->json([
                    'kabupaten' => $kabupaten,
                    'success' => true
                ]);
            }

            Log::warning('Provinsi not found:', [
                'requested_provinsi' => $provinsi,
                'available_provinces' => array_keys($regionData)
            ]);

            return response()->json([
                'kabupaten' => [],
                'error' => 'Provinsi not found',
                'available_provinces' => array_keys($regionData)
            ]);

        } catch (\Exception $e) {
            Log::error('Get Kabupaten Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'kabupaten' => [],
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // API untuk mendapatkan kode FAT berdasarkan provinsi dan kabupaten - DENGAN DEBUGGING
    public function getKodeFat(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');
            $kabupaten = $request->input('kabupaten');

            // Log untuk debugging
            Log::info('Get Kode FAT Request:', [
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'all_params' => $request->all()
            ]);

            // Validasi input
            if (empty($provinsi) || empty($kabupaten)) {
                return response()->json([
                    'kode_fat' => '',
                    'error' => 'Provinsi and kabupaten parameters are required'
                ], 400);
            }

            $regionData = $this->getRegionData();

            if (isset($regionData[$provinsi][$kabupaten])) {
                // Ambil jumlah pelanggan yang sudah ada di kabupaten ini untuk numbering
                $count = Pelanggan::where('provinsi', $provinsi)
                                 ->where('kabupaten', $kabupaten)
                                 ->count();

                $kodeFat = $regionData[$provinsi][$kabupaten] . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

                Log::info('Kode FAT Generated:', [
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'existing_count' => $count,
                    'kode_fat' => $kodeFat
                ]);

                return response()->json([
                    'kode_fat' => $kodeFat,
                    'success' => true
                ]);
            }

            Log::warning('Provinsi/Kabupaten combination not found:', [
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten
            ]);

            return response()->json([
                'kode_fat' => '',
                'error' => 'Provinsi/Kabupaten combination not found'
            ]);

        } catch (\Exception $e) {
            Log::error('Get Kode FAT Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'kode_fat' => '',
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
        // Ambil data pelanggan agar bisa ditampilkan di tabel
        $pelanggans = Pelanggan::orderBy('created_at', 'desc')->paginate(10);

        // Ambil data competitor untuk dropdown cluster dan kecepatan
        $competitors = Competitor::select('cluster', 'kecepatan')->distinct()->get();

        return view('report.operational.index', compact('pelanggans', 'competitors'));
    }

    // Method untuk mendapatkan kecepatan berdasarkan cluster via AJAX
    public function getKecepatanByBandwidth(Request $request)
    {
        $kecepatan = $request->get('kecepatan');

        $paket = Competitor::where('kecepatan', $kecepatan)
                        ->select('speed')
                        ->distinct()
                        ->pluck('speed');

        return response()->json($paket);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'alamat'         => 'required|string',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'nomor_telepon'  => 'required|string|max:50',
            'cluster'        => 'required|string|max:100',
            'kode_fat'       => 'nullable|string|max:100',
        ]);

        $validated['id_pelanggan'] = $this->generateNextCustomerId();

        // Otomatis isi kecepatan sama dengan bandwidth pelanggan
        $validated['kecepatan'] = [$validated['bandwidth']];

        // Generate kode FAT otomatis jika kosong
        if (empty($validated['kode_fat'])) {
            $regionData = $this->getRegionData();
            if (isset($regionData[$validated['provinsi']][$validated['kabupaten']])) {
                $count = Pelanggan::where('provinsi', $validated['provinsi'])
                                 ->where('kabupaten', $validated['kabupaten'])
                                 ->count();

                $validated['kode_fat'] = $regionData[$validated['provinsi']][$validated['kabupaten']] . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            }
        }

        try {
            Pelanggan::create($validated);
            return redirect()->route('report.operational.index')
                ->with('success', "Data pelanggan {$validated['nama_pelanggan']} berhasil disimpan dengan kode FAT: {$validated['kode_fat']}!");
        } catch (\Exception $e) {
            Log::error('Store Pelanggan Error:', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            // Cari berdasarkan id_pelanggan
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();

            // Data region untuk dropdown
            $regionData = $this->getRegionData();

            Log::info("Form edit dibuka untuk pelanggan: {$pelanggan->nama_pelanggan} (ID: {$id})");

            return view('report.operational.edit', compact('pelanggan', 'regionData'));

        } catch (\Exception $e) {
            Log::error("Gagal membuka form edit pelanggan ID {$id}: " . $e->getMessage());

            return redirect()
                ->route('report.operational.index')
                ->withErrors(['error' => 'Data pelanggan tidak ditemukan. ID: ' . $id]);
        }
    }

    public function update(Request $request, $id)
    {
        // Log untuk debugging
        Log::info("Attempting to update pelanggan with ID: {$id}");
        Log::info("Request data: " . json_encode($request->all()));

        // Validasi input
        $validated = $request->validate([
            'id_pelanggan'   => [
                'required',
                'string',
                'max:100',
                // Unique validation yang benar untuk primary key non-increment
                \Illuminate\Validation\Rule::unique('pelanggans', 'id_pelanggan')->ignore($id, 'id_pelanggan')
            ],
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'nomor_telepon'  => 'required|string|max:50',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'kode_fat'       => 'nullable|string|max:100',
            'alamat'         => 'required|string',
            'cluster'        => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi',
            'id_pelanggan.unique' => 'ID Pelanggan sudah digunakan oleh pelanggan lain',
            'nama_pelanggan.required' => 'Nama Pelanggan wajib diisi',
            'bandwidth.required' => 'Bandwidth wajib diisi',
            'nomor_telepon.required' => 'Nomor Telepon wajib diisi',
            'provinsi.required' => 'Provinsi wajib dipilih',
            'kabupaten.required' => 'Kabupaten wajib dipilih',
            'alamat.required' => 'Alamat wajib diisi',
            'cluster.required' => 'Cluster wajib dipilih',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
        ]);

        try {
            // Cari pelanggan berdasarkan id_pelanggan
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();

            // Jika ID pelanggan diubah, perlu update primary key
            if ($validated['id_pelanggan'] !== $id) {
                // Cek apakah ID baru sudah ada
                $exists = Pelanggan::where('id_pelanggan', $validated['id_pelanggan'])->exists();
                if ($exists) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['id_pelanggan' => 'ID Pelanggan sudah digunakan']);
                }

                // Hapus data lama dan buat baru dengan ID baru
                DB::transaction(function () use ($pelanggan, $validated) {
                    $oldId = $pelanggan->id_pelanggan;
                    $pelanggan->delete();
                    Pelanggan::create($validated);
                    Log::info("ID Pelanggan berhasil diubah dari {$oldId} ke {$validated['id_pelanggan']}");
                });
            } else {
                // Update biasa jika ID tidak berubah
                $pelanggan->update($validated);
            }

            Log::info("Data pelanggan berhasil diupdate: {$validated['nama_pelanggan']} (ID: {$validated['id_pelanggan']})");

            return redirect()
                ->route('report.operational.index')
                ->with('success', "Data pelanggan {$validated['nama_pelanggan']} berhasil diperbarui!");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Pelanggan tidak ditemukan dengan ID: {$id}");

            return redirect()
                ->route('report.operational.index')
                ->withErrors(['error' => 'Data pelanggan tidak ditemukan']);

        } catch (\Exception $e) {
            Log::error("Gagal mengupdate pelanggan ID {$id}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function destroy($pelanggan)
    {
        try {
            $pelangganData = Pelanggan::findOrFail($pelanggan);
            $nama = $pelangganData->nama_pelanggan;
            $pelangganData->delete();
            return back()->with('success', "Data pelanggan {$nama} berhasil dihapus!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
