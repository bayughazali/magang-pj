@extends('layouts.app')

@section('title', 'Export Data')

@push('styles')
<style>
    .export-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
    }
    
    .export-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .preview-table {
        font-size: 0.85rem;
    }
    
    .filter-section {
        background: #f8f9fc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .btn-export {
        min-width: 120px;
    }
    
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
    }
    
    .loading-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-download mr-2"></i>Export Data
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Export Data</li>
            </ol>
        </nav>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card stats-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                {{ number_format($totalUsers) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Users Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($recentUsers) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Form -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter mr-2"></i>Filter & Export Data
                    </h6>
                </div>
                <div class="card-body">
                    <form id="exportForm" class="filter-section">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Tanggal Dari</label>
                                <input type="date" class="form-control" id="date_from" name="date_from">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Tanggal Sampai</label>
                                <input type="date" class="form-control" id="date_to" name="date_to">
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="">Semua Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                    <option value="moderator">Moderator</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="per_page" class="form-label">Preview Per Halaman</label>
                                <select class="form-control" id="per_page" name="per_page">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info btn-export" id="previewBtn">
                                    <i class="fas fa-eye mr-2"></i>Preview Data
                                </button>
                                <button type="button" class="btn btn-success btn-export" id="exportCsvBtn">
                                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                                </button>
                                <button type="button" class="btn btn-danger btn-export" id="exportPdfBtn">
                                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                                </button>
                                <button type="button" class="btn btn-secondary" id="resetBtn">
                                    <i class="fas fa-undo mr-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="row" id="previewSection" style="display: none;">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table mr-2"></i>Preview Data
                        <span class="badge badge-info ml-2" id="totalExportBadge">0</span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered preview-table" id="previewTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="previewTableBody">
                                <!-- Data akan diisi via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div id="previewPagination" class="d-flex justify-content-center mt-3">
                        <!-- Pagination akan diisi via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <h5>Sedang memproses export...</h5>
        <p class="text-muted">Mohon tunggu sebentar</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    
    // Preview data
    $('#previewBtn').click(function() {
        previewData(1);
    });
    
    // Export CSV
    $('#exportCsvBtn').click(function() {
        exportData('csv');
    });
    
    // Export PDF
    $('#exportPdfBtn').click(function() {
        exportData('pdf');
    });
    
    // Reset form
    $('#resetBtn').click(function() {
        $('#exportForm')[0].reset();
        $('#previewSection').hide();
    });
    
    // Preview data function
    function previewData(page = 1) {
        const formData = new FormData($('#exportForm')[0]);
        formData.append('page', page);
        
        $('#previewBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...');
        
        $.ajax({
            url: '{{ route("export.preview") }}',
            method: 'GET',
            data: Object.fromEntries(formData),
            success: function(response) {
                if (response.status === 'success') {
                    displayPreview(response.data);
                    $('#previewSection').show();
                    $('html, body').animate({
                        scrollTop: $('#previewSection').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan saat memuat preview', 'error');
            },
            complete: function() {
                $('#previewBtn').prop('disabled', false).html('<i class="fas fa-eye mr-2"></i>Preview Data');
            }
        });
    }
    
    // Display preview data
    function displayPreview(data) {
        let tbody = $('#previewTableBody');
        tbody.empty();
        
        $('#totalExportBadge').text(data.total_export + ' data');
        
        if (data.users.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data yang ditemukan</td>
                </tr>
            `);
            return;
        }
        
        let startNo = ((data.pagination.current_page - 1) * data.pagination.per_page) + 1;
        
        data.users.forEach(function(user, index) {
            let status = user.email_verified_at ? 
                '<span class="badge badge-success">Aktif</span>' : 
                '<span class="badge badge-secondary">Tidak Aktif</span>';
            
            let createdAt = new Date(user.created_at).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            tbody.append(`
                <tr>
                    <td>${startNo + index}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td><span class="badge badge-primary">${user.role || 'USER'}</span></td>
                    <td>${createdAt}</td>
                    <td>${status}</td>
                </tr>
            `);
        });
        
        // Update pagination
        updatePagination(data.pagination);
    }
    
    // Update pagination
    function updatePagination(pagination) {
        let paginationHtml = '<nav><ul class="pagination">';
        
        // Previous button
        if (pagination.current_page > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="previewData(${pagination.current_page - 1})">Previous</a></li>`;
        }
        
        // Page numbers
        let startPage = Math.max(1, pagination.current_page - 2);
        let endPage = Math.min(pagination.last_page, pagination.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            let activeClass = i === pagination.current_page ? 'active' : '';
            paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="previewData(${i})">${i}</a></li>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="previewData(${pagination.current_page + 1})">Next</a></li>`;
        }
        
        paginationHtml += '</ul></nav>';
        $('#previewPagination').html(paginationHtml);
    }
    
    // Export data function
    function exportData(format) {
        const formData = new FormData($('#exportForm')[0]);
        
        // Show loading
        $('#loadingOverlay').show();
        
        // Create URL with parameters
        let params = new URLSearchParams(Object.fromEntries(formData));
        let url = format === 'csv' ? '{{ route("export.csv") }}' : '{{ route("export.pdf") }}';
        
        // Create temporary link and trigger download
        let downloadUrl = url + '?' + params.toString();
        
        let link = document.createElement('a');
        link.href = downloadUrl;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Hide loading after delay
        setTimeout(() => {
            $('#loadingOverlay').hide();
            Swal.fire({
                title: 'Success!',
                text: `Data berhasil diexport ke ${format.toUpperCase()}`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }, 2000);
    }
    
    // Set max date to today
    $('#date_from, #date_to').attr('max', new Date().toISOString().split('T')[0]);
    
    // Validate date range
    $('#date_from, #date_to').change(function() {
        let dateFrom = $('#date_from').val();
        let dateTo = $('#date_to').val();
        
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            Swal.fire('Error', 'Tanggal dari tidak boleh lebih besar dari tanggal sampai', 'error');
            $(this).val('');
        }
    });
});

// Global function for pagination
function previewData(page) {
    $(document).ready(function() {
        const formData = new FormData($('#exportForm')[0]);
        formData.append('page', page);
        
        $.ajax({
            url: '{{ route("export.preview") }}',
            method: 'GET', 
            data: Object.fromEntries(formData),
            success: function(response) {
                if (response.status === 'success') {
                    displayPreview(response.data);
                }
            }
        });
    });
}
</script>
@endpush