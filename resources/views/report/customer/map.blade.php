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
// Global variables
let map = null;
let markers = [];
let markerClusterGroup = null;
let customerData = null;
let mapInitialized = false;

// Hide loading overlay
function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}

// Show loading overlay
function showLoading(message = 'Memuat peta...') {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.innerHTML = `
            <div class="text-center">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>
                <small>${message}</small>
            </div>
        `;
        loadingOverlay.style.display = 'flex';
    }
}

// Debug function
function debugMap() {
    const debugInfo = {
        timestamp: new Date().toISOString(),
        mapExists: !!map,
        mapInitialized: mapInitialized,
        leafletLoaded: typeof L !== 'undefined',
        leafletVersion: typeof L !== 'undefined' ? L.version : 'Not loaded',
        markerClusterLoaded: typeof L !== 'undefined' && typeof L.markerClusterGroup !== 'undefined',
        customerDataCount: customerData ? customerData.length : 0,
        markersCount: markers.length,
        containerExists: !!document.getElementById('customerMap'),
        containerDimensions: {
            width: document.getElementById('customerMap')?.offsetWidth || 0,
            height: document.getElementById('customerMap')?.offsetHeight || 0
        },
        validCustomers: customerData ? customerData.filter(c => 
            c.latitude && c.longitude && 
            !isNaN(parseFloat(c.latitude)) && 
            !isNaN(parseFloat(c.longitude)) &&
            parseFloat(c.latitude) !== 0 && 
            parseFloat(c.longitude) !== 0
        ).length : 0,
        sampleData: customerData ? customerData.slice(0, 2) : null,
        errors: window.mapErrors || []
    };
    
    document.getElementById('debugContent').textContent = JSON.stringify(debugInfo, null, 2);
    document.getElementById('debugInfo').style.display = 'block';
    console.log('Debug Info:', debugInfo);
    
    // Also check network connectivity
    fetch('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js')
        .then(response => {
            console.log('Leaflet CDN accessible:', response.ok);
        })
        .catch(error => {
            console.error('Leaflet CDN not accessible:', error);
        });
}

// Enhanced error handling
window.mapErrors = [];

function logError(error, context = '') {
    const errorInfo = {
        message: error.message || error,
        context: context,
        timestamp: new Date().toISOString(),
        stack: error.stack || ''
    };
    window.mapErrors.push(errorInfo);
    console.error(`Map Error [${context}]:`, error);
}

// Check if required libraries are loaded
function checkDependencies() {
    const checks = {
        leaflet: typeof L !== 'undefined',
        markerCluster: typeof L !== 'undefined' && typeof L.markerClusterGroup !== 'undefined',
        bootstrap: typeof bootstrap !== 'undefined'
    };
    
    console.log('Dependency checks:', checks);
    return checks;
}

// Wait for libraries with timeout
function waitForLibraries(callback, timeout = 10000) {
    const startTime = Date.now();
    
    function check() {
        const deps = checkDependencies();
        
        if (deps.leaflet && deps.markerCluster) {
            console.log('All dependencies loaded successfully');
            callback();
        } else if (Date.now() - startTime > timeout) {
            logError(new Error('Timeout waiting for libraries'), 'waitForLibraries');
            showError('Gagal memuat library peta. Periksa koneksi internet Anda.');
        } else {
            console.log('Still waiting for dependencies...', deps);
            setTimeout(check, 200);
        }
    }
    
    check();
}

// Show error message
function showError(message) {
    hideLoading();
    const mapContainer = document.getElementById('customerMap');
    if (mapContainer) {
        mapContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Error Memuat Peta</h5>
                    <p class="mb-3">${message}</p>
                    <div>
                        <button class="btn btn-primary btn-sm me-2" onclick="forceReload()">
                            <i class="fas fa-redo me-1"></i>Coba Lagi
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="debugMap()">
                            <i class="fas fa-bug me-1"></i>Debug
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
}

// Initialize map with better error handling
function initMap() {
    console.log('Starting map initialization...');
    showLoading('Menginisialisasi peta...');
    
    try {
        // Get customer data from server
        customerData = @json($pelanggans);
        console.log('Customer data loaded:', customerData?.length || 0, 'customers');
        
        if (!customerData || !Array.isArray(customerData)) {
            throw new Error('Data pelanggan tidak valid atau kosong');
        }
        
        // Destroy existing map
        if (map) {
            console.log('Removing existing map...');
            try {
                map.remove();
            } catch (e) {
                console.log('Error removing map:', e);
            }
            map = null;
            mapInitialized = false;
        }
        
        // Clear markers
        markers = [];
        markerClusterGroup = null;
        
        // Get map container
        const mapContainer = document.getElementById('customerMap');
        if (!mapContainer) {
            throw new Error('Map container element not found');
        }
        
        // Clear container and ensure proper sizing
        mapContainer.innerHTML = '';
        mapContainer.style.width = '100%';
        mapContainer.style.height = '70vh';
        mapContainer.style.minHeight = '500px';
        
        console.log('Map container prepared:', {
            width: mapContainer.offsetWidth,
            height: mapContainer.offsetHeight
        });
        
        if (mapContainer.offsetWidth === 0 || mapContainer.offsetHeight === 0) {
            throw new Error('Map container has zero dimensions');
        }
        
        showLoading('Membuat peta...');
        
        // Default center (Jakarta - since you're in Jakarta)
        let center = [-6.2088, 106.8456];
        let zoom = 11;
        
        // Find valid customer coordinates
        const validCustomers = customerData.filter(c => {
            const lat = parseFloat(c.latitude);
            const lng = parseFloat(c.longitude);
            return !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;
        });
        
        console.log('Valid customers found:', validCustomers.length);
        
        if (validCustomers.length > 0) {
            // Calculate center of all valid customers
            const latSum = validCustomers.reduce((sum, c) => sum + parseFloat(c.latitude), 0);
            const lngSum = validCustomers.reduce((sum, c) => sum + parseFloat(c.longitude), 0);
            center = [latSum / validCustomers.length, lngSum / validCustomers.length];
            zoom = validCustomers.length === 1 ? 15 : 12;
            console.log('Using calculated center:', center);
        }
        
        showLoading('Menginisialisasi kontrol peta...');
        
        // Create map with explicit options
        map = L.map(mapContainer, {
            center: center,
            zoom: zoom,
            zoomControl: true,
            attributionControl: true,
            preferCanvas: false,
            renderer: L.svg(),
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true
        });
        
        console.log('Map object created successfully');
        
        showLoading('Memuat tiles peta...');
        
        // Add tile layer with error handling
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            subdomains: ['a', 'b', 'c']
        });
        
        tileLayer.on('loading', () => {
            console.log('Tiles loading...');
        });
        
        tileLayer.on('load', () => {
            console.log('Tiles loaded successfully');
            hideLoading();
        });
        
        tileLayer.on('tileerror', (e) => {
            console.error('Tile load error:', e);
            logError(new Error('Failed to load map tiles'), 'tileLayer');
        });
        
        tileLayer.addTo(map);
        
        showLoading('Menginisialisasi marker cluster...');
        
        // Initialize marker cluster group
        markerClusterGroup = L.markerClusterGroup({
            chunkedLoading: true,
            maxClusterRadius: 80,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });
        
        console.log('Marker cluster group created');
        
        showLoading('Menambahkan marker pelanggan...');
        
        // Add markers
        addCustomerMarkers();
        
        // Map ready events
        map.whenReady(() => {
            console.log('Map is ready');
            mapInitialized = true;
            hideLoading();
            
            // Force size invalidation
            setTimeout(() => {
                if (map) {
                    map.invalidateSize(true);
                    if (markers.length > 0) {
                        fitAllMarkers();
                    }
                }
            }, 100);
        });
        
        // Handle map errors
        map.on('error', (e) => {
            console.error('Map error:', e);
            logError(e.error || new Error('Unknown map error'), 'map.error');
        });
        
        console.log('Map initialization completed');
        
    } catch (error) {
        logError(error, 'initMap');
        showError(`Gagal menginisialisasi peta: ${error.message}`);
    }
}

// Add customer markers with better error handling
function addCustomerMarkers() {
    try {
        markers = [];
        
        if (!customerData || customerData.length === 0) {
            console.log('No customer data available');
            hideLoading();
            showNoDataMessage();
            return;
        }
        
        let validMarkersCount = 0;
        let totalCustomers = customerData.length;
        
        console.log(`Processing ${totalCustomers} customers...`);
        
        customerData.forEach((customer, index) => {
            try {
                // Validate coordinates
                if (!customer.latitude || !customer.longitude) {
                    console.log(`Customer ${index}: Missing coordinates`);
                    return;
                }
                
                const lat = parseFloat(customer.latitude);
                const lng = parseFloat(customer.longitude);
                
                if (isNaN(lat) || isNaN(lng)) {
                    console.log(`Customer ${index}: Invalid coordinates (${customer.latitude}, ${customer.longitude})`);
                    return;
                }
                
                if (lat === 0 && lng === 0) {
                    console.log(`Customer ${index}: Zero coordinates`);
                    return;
                }
                
                // Validate coordinate ranges (rough world bounds)
                if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                    console.log(`Customer ${index}: Coordinates out of bounds (${lat}, ${lng})`);
                    return;
                }
                
                // Create custom marker icon
                const customIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `
                        <div style="
                            background: ${customer.kode_fat ? '#28a745' : '#ffc107'};
                            width: 24px;
                            height: 24px;
                            border-radius: 50%;
                            border: 3px solid white;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 10px;
                            font-weight: bold;
                        ">
                            ${customer.kode_fat ? 'F' : '?'}
                        </div>
                    `,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                    popupAnchor: [0, -12]
                });
                
                // Create marker
                const marker = L.marker([lat, lng], {
                    icon: customIcon,
                    title: customer.nama_pelanggan || `Customer ${index + 1}`
                });
                
                // Create popup content
                const popupContent = createPopupContent(customer);
                marker.bindPopup(popupContent, {
                    maxWidth: 300,
                    className: 'custom-popup'
                });
                
                // Add to arrays
                markers.push(marker);
                if (markerClusterGroup) {
                    markerClusterGroup.addLayer(marker);
                }
                
                validMarkersCount++;
                
                if (validMarkersCount % 10 === 0) {
                    console.log(`Processed ${validMarkersCount}/${totalCustomers} customers...`);
                }
                
            } catch (error) {
                logError(error, `addMarker-customer-${index}`);
            }
        });
        
        console.log(`Successfully processed ${validMarkersCount} out of ${totalCustomers} customers`);
        
        if (validMarkersCount > 0 && map && markerClusterGroup) {
            map.addLayer(markerClusterGroup);
            console.log('Marker cluster group added to map');
            
            // Update info panel
            updateInfoPanel(validMarkersCount);
        } else {
            console.log('No valid markers to display');
            showNoDataMessage();
        }
        
        hideLoading();
        
    } catch (error) {
        logError(error, 'addCustomerMarkers');
        hideLoading();
        showError('Gagal menambahkan marker pelanggan');
    }
}

// Update info panel
function updateInfoPanel(validCount) {
    // You can update the customer info panel here if needed
    console.log(`Map now shows ${validCount} valid customer locations`);
}

// Create popup content
function createPopupContent(customer) {
    const fatBadge = customer.kode_fat ? 
        `<span class="badge bg-success">${customer.kode_fat}</span>` : 
        `<span class="badge bg-warning text-dark">Tidak Ada</span>`;
    
    return `
        <div style="min-width: 250px;">
            <h6 class="text-primary mb-2">${customer.nama_pelanggan || 'Nama tidak tersedia'}</h6>
            <table class="table table-sm">
                <tr><td><strong>ID:</strong></td><td>${customer.id_pelanggan || 'N/A'}</td></tr>
                <tr><td><strong>Bandwidth:</strong></td><td>${customer.bandwidth || 'N/A'}</td></tr>
                <tr><td><strong>Cluster:</strong></td><td>${customer.cluster || 'N/A'}</td></tr>
                <tr><td><strong>FAT:</strong></td><td>${fatBadge}</td></tr>
                <tr><td><strong>Telepon:</strong></td><td>${customer.nomor_telepon || 'N/A'}</td></tr>
            </table>
            <div class="mt-2">
                <small><strong>Alamat:</strong> ${customer.alamat || 'Tidak tersedia'}</small>
            </div>
            <div class="mt-2">
                <small><strong>Koordinat:</strong> ${customer.latitude}, ${customer.longitude}</small>
            </div>
            <div class="mt-2 d-grid">
                <button class="btn btn-sm btn-primary" onclick="showCustomerDetail(${customer.id})">
                    Detail Lengkap
                </button>
            </div>
        </div>
    `;
}

// Show no data message
function showNoDataMessage() {
    hideLoading();
    if (map) {
        const control = L.control({position: 'topleft'});
        control.onAdd = function() {
            const div = L.DomUtil.create('div', 'leaflet-control');
            div.innerHTML = `
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Tidak ada pelanggan dengan koordinat GPS valid
                </div>
            `;
            div.style.background = 'white';
            div.style.padding = '10px';
            div.style.borderRadius = '5px';
            div.style.boxShadow = '0 1px 5px rgba(0,0,0,0.4)';
            return div;
        };
        control.addTo(map);
    }
}

// Fit all markers in view
function fitAllMarkers() {
    if (!map || !mapInitialized) {
        console.log('Map not ready for fitting markers');
        return;
    }
    
    try {
        if (markers.length === 0) {
            console.log('No markers to fit');
            return;
        }
        
        if (markers.length === 1) {
            // Single marker - just center on it
            const marker = markers[0];
            map.setView(marker.getLatLng(), 15);
            console.log('Centered on single marker');
        } else {
            // Multiple markers - fit bounds
            const group = new L.featureGroup(markers);
            const bounds = group.getBounds();
            
            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    padding: [20, 20],
                    maxZoom: 16
                });
                console.log('Fitted bounds for', markers.length, 'markers');
            } else {
                console.log('Invalid bounds, using default view');
                map.setView([-6.2088, 106.8456], 11);
            }
        }
        
    } catch (error) {
        logError(error, 'fitAllMarkers');
    }
}

// Find user location
function findMyLocation() {
    if (!navigator.geolocation) {
        alert('Geolocation tidak didukung browser Anda');
        return;
    }
    
    showLoading('Mencari lokasi Anda...');
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            hideLoading();
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            if (map) {
                map.setView([lat, lng], 15);
                
                L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'user-location',
                        html: '<div style="background: #007bff; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map).bindPopup('Lokasi Anda').openPopup();
            }
        },
        (error) => {
            hideLoading();
            alert('Tidak dapat menentukan lokasi: ' + error.message);
        },
        {
            timeout: 10000,
            enableHighAccuracy: true
        }
    );
}

// Toggle clustering
function toggleClusters() {
    if (!map || !markerClusterGroup || !mapInitialized) {
        console.log('Map or cluster group not ready');
        return;
    }
    
    try {
        if (map.hasLayer(markerClusterGroup)) {
            // Remove cluster, add individual markers
            map.removeLayer(markerClusterGroup);
            markers.forEach(marker => {
                if (marker) {
                    map.addLayer(marker);
                }
            });
            console.log('Clustering disabled');
        } else {
            // Remove individual markers, add cluster
            markers.forEach(marker => {
                if (marker && map.hasLayer(marker)) {
                    map.removeLayer(marker);
                }
            });
            map.addLayer(markerClusterGroup);
            console.log('Clustering enabled');
        }
    } catch (error) {
        logError(error, 'toggleClusters');
    }
}

// Show customer detail
function showCustomerDetail(customerId) {
    try {
        const modal = new bootstrap.Modal(document.getElementById('customerDetailModal'));
        const modalBody = document.getElementById('customerDetailBody');
        
        modalBody.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>';
        modal.show();
        
        // Find customer data
        const customer = customerData ? customerData.find(c => c.id == customerId) : null;
        
        setTimeout(() => {
            if (customer) {
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Dasar</h6>
                            <table class="table table-sm">
                                <tr><td>ID Pelanggan</td><td>${customer.id_pelanggan || 'N/A'}</td></tr>
                                <tr><td>Nama</td><td>${customer.nama_pelanggan || 'N/A'}</td></tr>
                                <tr><td>Bandwidth</td><td>${customer.bandwidth || 'N/A'}</td></tr>
                                <tr><td>Cluster</td><td>${customer.cluster || 'N/A'}</td></tr>
                                <tr><td>Kode FAT</td><td>${customer.kode_fat || 'Tidak Ada'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Kontak & Lokasi</h6>
                            <table class="table table-sm">
                                <tr><td>Telepon</td><td>${customer.nomor_telepon || 'N/A'}</td></tr>
                                <tr><td>Alamat</td><td>${customer.alamat || 'N/A'}</td></tr>
                                <tr><td>Latitude</td><td>${customer.latitude || 'N/A'}</td></tr>
                                <tr><td>Longitude</td><td>${customer.longitude || 'N/A'}</td></tr>
                            </table>
                        </div>
                    </div>
                `;
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Data pelanggan tidak ditemukan</div>';
            }
        }, 300);
        
    } catch (error) {
        logError(error, 'showCustomerDetail');
    }
}

// Force reload function
function forceReload() {
    console.log('Force reloading map...');
    
    // Clear everything
    if (map) {
        try {
            map.remove();
        } catch (e) {
            console.log('Error removing map during force reload:', e);
        }
    }
    
    map = null;
    markers = [];
    markerClusterGroup = null;
    mapInitialized = false;
    window.mapErrors = [];
    
    // Reset container
    const mapContainer = document.getElementById('customerMap');
    if (mapContainer) {
        mapContainer.innerHTML = '';
    }
    
    // Hide debug info
    document.getElementById('debugInfo').style.display = 'none';
    
    // Restart initialization
    showLoading('Memuat ulang peta...');
    setTimeout(() => {
        initMap();
    }, 500);
}

// Main initialization function
function startMapApplication() {
    console.log('=== Starting Map Application ===');
    console.log('Current time:', new Date().toISOString());
    
    const deps = checkDependencies();
    console.log('Dependencies check:', deps);
    
    if (!deps.leaflet) {
        showError('Leaflet library tidak termuat. Periksa koneksi internet.');
        return;
    }
    
    if (!deps.markerCluster) {
        showError('MarkerCluster library tidak termuat. Periksa koneksi internet.');
        return;
    }
    
    // Initialize map
    try {
        initMap();
    } catch (error) {
        logError(error, 'startMapApplication');
        showError('Gagal memulai aplikasi peta');
    }
}

// Enhanced DOM ready handler
function onDOMReady() {
    console.log('DOM Content Loaded');
    
    // Ensure container exists
    const mapContainer = document.getElementById('customerMap');
    if (!mapContainer) {
        console.error('Map container not found in DOM');
        return;
    }
    
    // Check container dimensions
    const rect = mapContainer.getBoundingClientRect();
    console.log('Map container dimensions:', {
        width: rect.width,
        height: rect.height,
        offsetWidth: mapContainer.offsetWidth,
        offsetHeight: mapContainer.offsetHeight
    });
    
    // Wait for libraries with better timeout handling
    waitForLibraries(() => {
        console.log('All libraries loaded, starting map application...');
        
        // Small delay to ensure DOM is fully rendered
        setTimeout(() => {
            startMapApplication();
        }, 100);
        
    }, 15000); // 15 second timeout
}

// Multiple initialization attempts
document.addEventListener('DOMContentLoaded', onDOMReady);

// Backup initialization on window load
window.addEventListener('load', function() {
    console.log('Window fully loaded');
    
    // If map hasn't been initialized yet, try again
    if (!mapInitialized && !map) {
        console.log('Map not initialized on window load, retrying...');
        setTimeout(() => {
            if (!mapInitialized) {
                onDOMReady();
            }
        }, 500);
    }
    
    // Force size invalidation
    if (map) {
        setTimeout(() => {
            map.invalidateSize(true);
        }, 100);
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (map && mapInitialized) {
        setTimeout(() => {
            map.invalidateSize(true);
        }, 100);
    }
});

// Handle visibility change
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && map && mapInitialized) {
        setTimeout(() => {
            map.invalidateSize(true);
        }, 200);
    }
});

// Handle page focus
window.addEventListener('focus', function() {
    if (map && mapInitialized) {
        setTimeout(() => {
            map.invalidateSize(true);
        }, 100);
    }
});

// Error event listeners
window.addEventListener('error', function(e) {
    logError(e.error || new Error(e.message), 'window.error');
});

window.addEventListener('unhandledrejection', function(e) {
    logError(e.reason || new Error('Unhandled promise rejection'), 'unhandledrejection');
});

// Expose functions globally for debugging
window.debugMap = debugMap;
window.forceReload = forceReload;
window.fitAllMarkers = fitAllMarkers;
window.findMyLocation = findMyLocation;
window.toggleClusters = toggleClusters;
window.showCustomerDetail = showCustomerDetail;

console.log('Map script loaded and ready');
</script>

<!-- Fallback script in case main script fails -->
<script>
// Emergency fallback
setTimeout(function() {
    if (!mapInitialized && !map) {
        console.log('Emergency fallback triggered');
        
        const mapContainer = document.getElementById('customerMap');
        if (mapContainer && mapContainer.innerHTML.includes('Memuat peta')) {
            mapContainer.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                        <h5>Peta Tidak Dapat Dimuat</h5>
                        <p class="text-muted mb-3">Terjadi timeout saat memuat peta</p>
                        <div>
                            <button class="btn btn-primary btn-sm me-2" onclick="location.reload()">
                                <i class="fas fa-redo me-1"></i>Refresh Halaman
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="debugMap()">
                                <i class="fas fa-bug me-1"></i>Debug Info
                            </button>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                Jika masalah berlanjut, periksa:
                                <br>• Koneksi internet
                                <br>• Console browser (F12)
                                <br>• Data pelanggan
                            </small>
                        </div>
                    </div>
                </div>
            `;
        }
    }
}, 20000); // 20 second timeout
</script>
@endpush