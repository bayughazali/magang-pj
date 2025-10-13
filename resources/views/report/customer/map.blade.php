    {{-- resources/views/report/customer/map.blade.php --}}
    @extends('layouts.app')

    @push('styles')
    <!-- Leaflet CSS - Load early -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        /* Perbaikan CSS tambahan */
.customer-popup {
    min-width: 250px;
}

.popup-header {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 8px;
    margin-bottom: 8px;
}

.popup-body .table td {
    padding: 2px 0;
    vertical-align: top;
}

.custom-popup .leaflet-popup-content {
    margin: 8px 12px;
}

/* Loading overlay improvements */
.map-loading {
    backdrop-filter: blur(2px);
    background: rgba(248, 249, 250, 0.95);
}

/* Progress bar styling */
.progress {
    background-color: rgba(0, 123, 255, 0.25);
}

.progress-bar {
    background-color: #007bff;
}
    .custom-marker {
        background: transparent !important;
        border: none !important;
    }

    #customerMap {
        position: relative;
        z-index: 1;
        background: #f8f9fa;
        width: 100% !important;
        height: 70vh !important;
        min-height: 500px !important;
    }

    .map-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #6c757d;
        z-index: 1000;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }

    .marker-cluster-small {
        background-color: rgba(181, 226, 140, 0.6);
    }
    .marker-cluster-small div {
        background-color: rgba(110, 204, 57, 0.6);
    }
    .marker-cluster-medium {
        background-color: rgba(241, 211, 87, 0.6);
    }
    .marker-cluster-medium div {
        background-color: rgba(240, 194, 12, 0.6);
    }
    .marker-cluster-large {
        background-color: rgba(253, 156, 115, 0.6);
    }
    .marker-cluster-large div {
        background-color: rgba(241, 128, 23, 0.6);
    }

    /* Fix for map container */
    .leaflet-container {
        background: #e5e3df;
    }
    </style>
    @endpush

    @section('content')
    <div class="container-fluid mt-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marked-alt fa-2x me-3"></i>
                                <div>
                                    <h4 class="mb-1">Peta Lokasi Pelanggan</h4>
                                    <p class="mb-0 opacity-75">Visualisasi lokasi pelanggan berdasarkan koordinat GPS</p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-0">{{ $pelanggans->count() }}</h5>
                                <small class="opacity-75">Lokasi Pelanggan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Controls -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" onclick="fitAllMarkers()">
                                    <i class="fas fa-expand-alt me-1"></i>Lihat Semua
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="findMyLocation()">
                                    <i class="fas fa-location-arrow me-1"></i>Lokasi Saya
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="toggleClusters()">
                                    <i class="fas fa-layer-group me-1"></i>Toggle Cluster
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="debugMap()">
                                    <i class="fas fa-bug me-1"></i>Debug
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="forceReload()">
                                    <i class="fas fa-redo me-1"></i>Force Reload
                                </button>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customer.search') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debug Info (hidden by default) -->
        <div class="row mb-3" id="debugInfo" style="display: none;">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">Debug Information</h6>
                    </div>
                    <div class="card-body">
                        <pre id="debugContent"></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div id="mapWrapper" style="position: relative; height: 70vh; min-height: 500px;">
                            <div id="customerMap"></div>
                            <div id="loadingOverlay" class="map-loading">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>
                                    <small>Memuat peta...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info Panel -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informasi Pelanggan di Peta
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row" id="customerInfo">
                            <div class="col-md-3 text-center">
                                <div class="border-end pe-3">
                                    <h4 class="text-primary mb-1">{{ $pelanggans->count() }}</h4>
                                    <small class="text-muted">Total Pelanggan</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="border-end pe-3">
                                    <h4 class="text-success mb-1">{{ $pelanggans->whereNotNull('kode_fat')->count() }}</h4>
                                    <small class="text-muted">Dengan Kode FAT</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="border-end pe-3">
                                    <h4 class="text-info mb-1">{{ $pelanggans->groupBy('cluster')->count() }}</h4>
                                    <small class="text-muted">Cluster Aktif</small>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <h4 class="text-warning mb-1">{{ $pelanggans->whereNull('kode_fat')->count() }}</h4>
                                <small class="text-muted">Tanpa Kode FAT</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Detail Modal -->
    <div class="modal fade" id="customerDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="customerDetailBody">
                    <!-- Detail akan dimuat di sini -->
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <!-- Load Leaflet with integrity check -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Then MarkerCluster -->
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
// Global variables dengan initialization yang lebih robust
let map = null;
let markers = [];
let markerClusterGroup = null;
let customerData = null;
let mapInitialized = false;
let initializationAttempts = 0;
const MAX_ATTEMPTS = 5;

// Fungsi untuk validasi data customer yang lebih ketat
function validateCustomerData(data) {
    console.log('Validating customer data:', data);
    
    if (!data || !Array.isArray(data)) {
        console.error('Data customer tidak valid atau kosong');
        return { isValid: false, validData: [], message: 'Data tidak tersedia' };
    }
    
    if (data.length === 0) {
        return { isValid: false, validData: [], message: 'Tidak ada data customer' };
    }
    
    const validData = data.filter(customer => {
        if (!customer) return false;
        
        const lat = parseFloat(customer.latitude);
        const lng = parseFloat(customer.longitude);
        
        // Validasi koordinat yang lebih ketat
        const isValidLat = !isNaN(lat) && lat !== 0 && lat >= -90 && lat <= 90;
        const isValidLng = !isNaN(lng) && lng !== 0 && lng >= -180 && lng <= 180;
        
        return isValidLat && isValidLng;
    });
    
    console.log(`Total data: ${data.length}, Data valid: ${validData.length}`);
    
    return {
        isValid: validData.length > 0,
        validData: validData,
        totalData: data.length,
        validCount: validData.length,
        message: validData.length === 0 ? 'Tidak ada customer dengan koordinat GPS yang valid' : 'Data valid ditemukan'
    };
}

// Fungsi loading yang lebih informatif
function showLoading(message = 'Memuat peta...') {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div><h6 class="text-primary">${message}</h6></div>
                <div class="mt-2">
                    <small class="text-muted">Percobaan ke-${initializationAttempts + 1} dari ${MAX_ATTEMPTS}</small>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             style="width: ${((initializationAttempts + 1) / MAX_ATTEMPTS) * 100}%"></div>
                    </div>
                </div>
            </div>
        `;
        loadingOverlay.style.display = 'flex';
    }
}

function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 500);
    }
}

// Fungsi untuk cek dependencies yang lebih detail
function checkDependencies() {
    const checks = {
        leaflet: typeof L !== 'undefined',
        leafletVersion: typeof L !== 'undefined' ? L.version : null,
        markerCluster: typeof L !== 'undefined' && typeof L.markerClusterGroup === 'function',
        mapContainer: !!document.getElementById('customerMap'),
        containerDimensions: getContainerDimensions(),
        browserSupport: {
            geolocation: 'geolocation' in navigator,
            localStorage: typeof Storage !== 'undefined'
        }
    };
    
    console.log('=== Dependency Checks ===', checks);
    return checks;
}

function getContainerDimensions() {
    const container = document.getElementById('customerMap');
    if (container) {
        return {
            width: container.offsetWidth,
            height: container.offsetHeight,
            clientWidth: container.clientWidth,
            clientHeight: container.clientHeight
        };
    }
    return null;
}

// Fungsi inisialisasi peta yang diperbaiki
function initializeMap() {
    try {
        console.log('=== Starting Map Initialization ===');
        showLoading('Menginisialisasi peta...');
        
        // Reset previous map if exists
        if (map) {
            try {
                map.remove();
                map = null;
            } catch (e) {
                console.log('Error removing previous map:', e);
            }
        }
        
        // Clear markers array
        markers = [];
        
        // Get and validate customer data
        customerData = @json($pelanggans);
        console.log('Raw customer data:', customerData);
        
        const validation = validateCustomerData(customerData);
        if (!validation.isValid) {
            showNoDataMessage(validation.message, validation.totalData, validation.validCount);
            return;
        }
        
        const validCustomers = validation.validData;
        showLoading(`Menambahkan ${validCustomers.length} marker...`);
        
        // Calculate center point
        const center = calculateMapCenter(validCustomers);
        const zoom = calculateInitialZoom(validCustomers);
        
        // Get map container
        const mapContainer = document.getElementById('customerMap');
        if (!mapContainer) {
            throw new Error('Container peta tidak ditemukan');
        }
        
        // Clear container
        mapContainer.innerHTML = '';
        
        // Create map with better options
        map = L.map(mapContainer, {
            center: center,
            zoom: zoom,
            zoomControl: true,
            attributionControl: true,
            preferCanvas: true, // Better performance for many markers
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true
        });
        
        // Add tile layer with error handling
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 3,
            timeout: 15000,
            retryLimit: 3,
            errorTileUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgZmlsbD0iI2VlZSIvPjwvc3ZnPg=='
        });
        
        // Tile layer event handlers
        tileLayer.on('loading', () => {
            console.log('Map tiles loading...');
        });
        
        tileLayer.on('load', () => {
            console.log('Map tiles loaded successfully');
        });
        
        tileLayer.on('tileerror', (e) => {
            console.warn('Tile loading error:', e);
        });
        
        // Add tile layer to map
        tileLayer.addTo(map);
        
        // Initialize marker clustering if available
        if (typeof L.markerClusterGroup === 'function') {
            markerClusterGroup = L.markerClusterGroup({
                chunkedLoading: true,
                chunkInterval: 200,
                chunkDelay: 50,
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true
            });
            map.addLayer(markerClusterGroup);
            addMarkersWithClustering(validCustomers);
        } else {
            console.log('MarkerCluster not available, using simple markers');
            addSimpleMarkers(validCustomers);
        }
        
        // Map ready event
        map.whenReady(() => {
            console.log('Map ready and initialized');
            mapInitialized = true;
            hideLoading();
            
            // Fit bounds after a short delay
            setTimeout(() => {
                fitAllMarkers();
            }, 1000);
        });
        
        // Error handling for map
        map.on('error', (e) => {
            console.error('Map error:', e);
            showError('Error pada peta: ' + e.message);
        });
        
    } catch (error) {
        console.error('Error in initializeMap:', error);
        showError('Gagal menginisialisasi peta: ' + error.message);
    }
}

// Fungsi untuk menghitung center point
function calculateMapCenter(validCustomers) {
    if (validCustomers.length === 0) {
        return [-8.4095, 115.1889]; // Default Bali center
    }
    
    if (validCustomers.length === 1) {
        return [parseFloat(validCustomers[0].latitude), parseFloat(validCustomers[0].longitude)];
    }
    
    const latSum = validCustomers.reduce((sum, c) => sum + parseFloat(c.latitude), 0);
    const lngSum = validCustomers.reduce((sum, c) => sum + parseFloat(c.longitude), 0);
    
    return [latSum / validCustomers.length, lngSum / validCustomers.length];
}

// Fungsi untuk menghitung zoom level
function calculateInitialZoom(validCustomers) {
    if (validCustomers.length <= 1) return 15;
    if (validCustomers.length <= 5) return 13;
    if (validCustomers.length <= 20) return 11;
    return 10;
}

// Fungsi untuk menambah markers dengan clustering
function addMarkersWithClustering(validCustomers) {
    try {
        console.log(`Adding ${validCustomers.length} markers with clustering...`);
        
        validCustomers.forEach((customer, index) => {
            const lat = parseFloat(customer.latitude);
            const lng = parseFloat(customer.longitude);
            
            // Create marker
            const marker = L.marker([lat, lng], {
                title: customer.nama_pelanggan || `Customer ${index + 1}`
            });
            
            // Create detailed popup
            const popupContent = createPopupContent(customer, index);
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            
            // Add to cluster group and markers array
            markerClusterGroup.addLayer(marker);
            markers.push(marker);
        });
        
        console.log(`Successfully added ${markers.length} markers to cluster`);
        mapInitialized = true;
        hideLoading();
        
    } catch (error) {
        console.error('Error adding clustered markers:', error);
        // Fallback to simple markers
        addSimpleMarkers(validCustomers);
    }
}

// Fungsi untuk menambah markers sederhana (tanpa clustering)
function addSimpleMarkers(validCustomers) {
    try {
        console.log(`Adding ${validCustomers.length} simple markers...`);
        
        validCustomers.forEach((customer, index) => {
            const lat = parseFloat(customer.latitude);
            const lng = parseFloat(customer.longitude);
            
            const marker = L.marker([lat, lng], {
                title: customer.nama_pelanggan || `Customer ${index + 1}`
            });
            
            const popupContent = createPopupContent(customer, index);
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            
            marker.addTo(map);
            markers.push(marker);
        });
        
        console.log(`Successfully added ${markers.length} simple markers`);
        mapInitialized = true;
        hideLoading();
        
    } catch (error) {
        console.error('Error adding simple markers:', error);
        showError('Gagal menambahkan marker ke peta');
    }
}

// Fungsi untuk membuat konten popup
function createPopupContent(customer, index) {
    const fatCode = customer.kode_fat || 'Tidak Ada';
    const bandwidth = customer.bandwidth || 'N/A';
    const cluster = customer.cluster || 'N/A';
    const alamat = customer.alamat || 'Alamat tidak tersedia';
    
    return `
        <div class="customer-popup">
            <div class="popup-header">
                <h6 class="text-primary mb-1">
                    <i class="fas fa-user me-1"></i>
                    ${customer.nama_pelanggan || `Customer ${index + 1}`}
                </h6>
            </div>
            <div class="popup-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">ID:</td>
                        <td><strong>${customer.id_pelanggan || 'N/A'}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Bandwidth:</td>
                        <td><span class="badge bg-info">${bandwidth}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cluster:</td>
                        <td><span class="badge bg-secondary">${cluster}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">FAT:</td>
                        <td><span class="badge ${fatCode === 'Tidak Ada' ? 'bg-warning' : 'bg-success'}">${fatCode}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Koordinat:</td>
                        <td><small>${customer.latitude}, ${customer.longitude}</small></td>
                    </tr>
                </table>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        ${alamat}
                    </small>
                </div>
            </div>
        </div>
    `;
}

// Show error message dengan retry options
function showError(message, canRetry = true) {
    hideLoading();
    const mapContainer = document.getElementById('customerMap');
    if (mapContainer) {
        const retryButton = canRetry && initializationAttempts < MAX_ATTEMPTS ? 
            `<button class="btn btn-primary btn-sm me-2" onclick="retryMapInitialization()">
                <i class="fas fa-redo me-1"></i>Coba Lagi (${MAX_ATTEMPTS - initializationAttempts} tersisa)
            </button>` : '';
        
        mapContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                    <h5 class="text-danger">Error Memuat Peta</h5>
                    <p class="mb-3 text-muted">${message}</p>
                    <div class="d-flex justify-content-center gap-2">
                        ${retryButton}
                        <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">
                            <i class="fas fa-refresh me-1"></i>Refresh Halaman
                        </button>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            Percobaan: ${initializationAttempts}/${MAX_ATTEMPTS}
                        </small>
                    </div>
                </div>
            </div>
        `;
    }
}

// Show no data message dengan informasi detail
function showNoDataMessage(message, totalData = 0, validCount = 0) {
    hideLoading();
    const mapContainer = document.getElementById('customerMap');
    if (mapContainer) {
        mapContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center p-4">
                    <i class="fas fa-info-circle fa-3x mb-3 text-info"></i>
                    <h5>Tidak Ada Data GPS</h5>
                    <p class="text-muted mb-3">${message}</p>
                    <div class="card text-start" style="max-width: 400px;">
                        <div class="card-body">
                            <h6 class="card-title">Informasi Data:</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-users me-2 text-primary"></i>Total Customer: <strong>${totalData}</strong></li>
                                <li><i class="fas fa-map-pin me-2 text-success"></i>Dengan GPS Valid: <strong>${validCount}</strong></li>
                                <li><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Tanpa GPS: <strong>${totalData - validCount}</strong></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" onclick="location.reload()">
                            <i class="fas fa-refresh me-1"></i>Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
}

// Retry function yang lebih smart
function retryMapInitialization() {
    if (initializationAttempts >= MAX_ATTEMPTS) {
        showError('Maksimal percobaan tercapai. Silakan refresh halaman.', false);
        return;
    }
    
    initializationAttempts++;
    console.log(`Retrying map initialization (attempt ${initializationAttempts}/${MAX_ATTEMPTS})`);
    
    // Reset state
    mapInitialized = false;
    
    // Progressive delay - semakin lama semakin delay
    const delay = Math.min(1000 * initializationAttempts, 5000);
    
    setTimeout(() => {
        // Check dependencies before retry
        const deps = checkDependencies();
        if (!deps.leaflet) {
            showError('Leaflet library belum tersedia. Menunggu...', true);
            setTimeout(() => retryMapInitialization(), 2000);
            return;
        }
        
        initializeMap();
    }, delay);
}

// Enhanced button functions dengan error handling
function fitAllMarkers() {
    if (!map) {
        console.log('Map not initialized');
        return;
    }
    
    if (markers.length === 0) {
        console.log('No markers to fit');
        return;
    }
    
    try {
        if (markers.length === 1) {
            map.setView(markers[0].getLatLng(), 15);
        } else if (markerClusterGroup && markerClusterGroup.getLayers().length > 0) {
            // Use cluster group bounds
            map.fitBounds(markerClusterGroup.getBounds(), { 
                padding: [20, 20],
                maxZoom: 16
            });
        } else {
            // Use individual markers bounds
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds(), { 
                padding: [20, 20],
                maxZoom: 16
            });
        }
    } catch (error) {
        console.error('Error fitting markers:', error);
    }
}

function findMyLocation() {
    if (!navigator.geolocation) {
        alert('Geolocation tidak didukung oleh browser Anda');
        return;
    }
    
    showLoading('Mencari lokasi Anda...');
    
    const options = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 60000 // Cache for 1 minute
    };
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            hideLoading();
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;
            
            if (map) {
                map.setView([lat, lng], 15);
                
                // Add location marker with accuracy circle
                const locationMarker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map);
                
                locationMarker.bindPopup(`
                    <div class="text-center">
                        <h6 class="text-primary">Lokasi Anda</h6>
                        <p class="mb-1"><small>Lat: ${lat.toFixed(6)}</small></p>
                        <p class="mb-1"><small>Lng: ${lng.toFixed(6)}</small></p>
                        <p class="mb-0"><small>Akurasi: ±${accuracy.toFixed(0)}m</small></p>
                    </div>
                `).openPopup();
                
                // Add accuracy circle
                if (accuracy < 1000) {
                    L.circle([lat, lng], {
                        radius: accuracy,
                        color: 'blue',
                        fillColor: 'blue',
                        fillOpacity: 0.1,
                        weight: 1
                    }).addTo(map);
                }
            }
        },
        (error) => {
            hideLoading();
            let message = 'Tidak dapat menentukan lokasi: ';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    message += 'Akses lokasi ditolak';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message += 'Informasi lokasi tidak tersedia';
                    break;
                case error.TIMEOUT:
                    message += 'Timeout dalam mencari lokasi';
                    break;
                default:
                    message += 'Error tidak diketahui';
                    break;
            }
            alert(message);
        },
        options
    );
}

function toggleClusters() {
    if (!map || !markerClusterGroup) {
        alert('Clustering tidak tersedia');
        return;
    }
    
    try {
        if (map.hasLayer(markerClusterGroup)) {
            // Remove clustering
            map.removeLayer(markerClusterGroup);
            markers.forEach(marker => marker.addTo(map));
            console.log('Clustering disabled');
        } else {
            // Enable clustering
            markers.forEach(marker => map.removeLayer(marker));
            map.addLayer(markerClusterGroup);
            console.log('Clustering enabled');
        }
    } catch (error) {
        console.error('Error toggling clusters:', error);
    }
}

function debugMap() {
    const validation = customerData ? validateCustomerData(customerData) : null;
    
    const debugInfo = {
        timestamp: new Date().toISOString(),
        mapStatus: {
            exists: !!map,
            initialized: mapInitialized,
            center: map ? map.getCenter() : null,
            zoom: map ? map.getZoom() : null
        },
        dependencies: checkDependencies(),
        data: {
            customerDataExists: !!customerData,
            customerDataLength: customerData ? customerData.length : 0,
            validation: validation,
            markersCount: markers.length,
            clusterGroupExists: !!markerClusterGroup
        },
        attempts: {
            current: initializationAttempts,
            max: MAX_ATTEMPTS
        },
        performance: {
            memoryUsage: performance.memory ? {
                used: Math.round(performance.memory.usedJSHeapSize / 1048576) + ' MB',
                total: Math.round(performance.memory.totalJSHeapSize / 1048576) + ' MB'
            } : 'Not available',
            timing: performance.now()
        }
    };
    
    document.getElementById('debugContent').textContent = JSON.stringify(debugInfo, null, 2);
    document.getElementById('debugInfo').style.display = 'block';
    console.log('=== DEBUG INFO ===', debugInfo);
}

function forceReload() {
    if (confirm('Yakin ingin me-reload halaman? Semua perubahan yang belum disimpan akan hilang.')) {
        location.reload(true);
    }
}

// Main initialization dengan multiple fallbacks
function startMapInitialization() {
    console.log('=== Starting Map Initialization Process ===');
    initializationAttempts = 0;
    
    const deps = checkDependencies();
    
    // Check critical dependencies
    if (!deps.mapContainer) {
        console.error('Map container not found');
        showError('Container peta tidak ditemukan', false);
        return;
    }
    
    if (!deps.leaflet) {
        console.log('Leaflet not ready, waiting...');
        showLoading('Menunggu library Leaflet...');
        
        // Wait for Leaflet with timeout
        let waitAttempts = 0;
        const maxWaitAttempts = 10;
        
        const waitForLeaflet = () => {
            if (typeof L !== 'undefined') {
                console.log('Leaflet ready, initializing map...');
                setTimeout(() => initializeMap(), 100);
            } else if (waitAttempts < maxWaitAttempts) {
                waitAttempts++;
                setTimeout(waitForLeaflet, 500);
            } else {
                showError('Leaflet library gagal dimuat. Silakan refresh halaman.', false);
            }
        };
        
        waitForLeaflet();
        return;
    }
    
    // All dependencies ready, start initialization
    initializeMap();
}

// Event listeners dengan improved handling
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, starting map initialization...');
    
    // Small delay untuk memastikan semua element sudah rendered
    setTimeout(() => {
        startMapInitialization();
    }, 300);
});

// Backup initialization
window.addEventListener('load', function() {
    if (!mapInitialized) {
        console.log('Window loaded, checking map status...');
        setTimeout(() => {
            if (!mapInitialized) {
                console.log('Map still not initialized, retrying...');
                retryMapInitialization();
            }
        }, 1000);
    }
});

// Emergency timeout dengan opsi retry
setTimeout(() => {
    if (!mapInitialized) {
        console.log('Emergency timeout triggered');
        showError('Timeout memuat peta. Coba refresh halaman atau gunakan tombol retry.', true);
    }
}, 20000);

// Expose functions globally
window.fitAllMarkers = fitAllMarkers;
window.findMyLocation = findMyLocation;
window.toggleClusters = toggleClusters;
window.debugMap = debugMap;
window.forceReload = forceReload;
window.retryMapInitialization = retryMapInitialization;

// Cleanup function
window.addEventListener('beforeunload', function() {
    if (map) {
        try {
            map.remove();
        } catch (e) {
            console.log('Error during cleanup:', e);
        }
    }
});

console.log('Enhanced map script loaded successfully');
</script>
    @endpush