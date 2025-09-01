@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-search fa-2x me-3"></i>
                            <div>
                                <h4 class="mb-1">Cari Pelanggan & Kode FAT</h4>
                                <p class="mb-0 opacity-75">Pencarian data pelanggan berdasarkan berbagai kriteria</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0">{{ $pelanggans->total() ?? 0 }}</h5>
                            <small class="opacity-75">Total Data</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error') || $errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') ?: $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filter Pencarian
                        </h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="showBasicSearch()">
                                <i class="fas fa-search me-1"></i>Basic
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="showAdvancedSearch()">
                                <i class="fas fa-sliders-h me-1"></i>Advanced
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Basic Search Form -->
                    <div id="basicSearchForm">
                        <form method="GET" action="{{ route('customer.search') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="filter_field" class="form-label fw-semibold">Filter Field</label>
                                    <select class="form-select" id="filter_field" name="filter_field">
                                        <option value="">Semua Field</option>
                                        <option value="id_pelanggan" {{ request('filter_field') == 'id_pelanggan' ? 'selected' : '' }}>ID Pelanggan</option>
                                        <option value="nama_pelanggan" {{ request('filter_field') == 'nama_pelanggan' ? 'selected' : '' }}>Nama Pelanggan</option>
                                        <option value="bandwidth" {{ request('filter_field') == 'bandwidth' ? 'selected' : '' }}>Bandwidth</option>
                                        <option value="alamat" {{ request('filter_field') == 'alamat' ? 'selected' : '' }}>Alamat</option>
                                        <option value="provinsi" {{ request('filter_field') == 'provinsi' ? 'selected' : '' }}>Provinsi</option>
                                        <option value="kabupaten" {{ request('filter_field') == 'kabupaten' ? 'selected' : '' }}>Kabupaten</option>
                                        <option value="nomor_telepon" {{ request('filter_field') == 'nomor_telepon' ? 'selected' : '' }}>Nomor Telepon</option>
                                        <option value="cluster" {{ request('filter_field') == 'cluster' ? 'selected' : '' }}>Cluster</option>
                                        <option value="kode_fat" {{ request('filter_field') == 'kode_fat' ? 'selected' : '' }}>Kode FAT</option>
                                        <option value="latitude" {{ request('filter_field') == 'latitude' ? 'selected' : '' }}>Latitude</option>
                                        <option value="longitude" {{ request('filter_field') == 'longitude' ? 'selected' : '' }}>Longitude</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="filter_query" class="form-label fw-semibold">Kata Kunci</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" id="filter_query" name="filter_query"
                                               placeholder="Masukkan kata kunci pencarian..."
                                               value="{{ $filterQuery ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="btn-group w-100">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>Cari
                                        </button>
                                        <a href="{{ route('customer.search') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Advanced Search Form (Hidden by default) -->
                    <div id="advancedSearchForm" style="display: none;">
                        <form method="GET" action="{{ route('customer.search.advanced') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cluster</label>
                                    <select class="form-select" name="cluster_filter">
                                        <option value="">Semua Cluster</option>
                                        @if(isset($pelanggans) && $pelanggans->count() > 0)
                                            @foreach($pelanggans->pluck('cluster')->unique()->sort() as $cluster)
                                                @if($cluster)
                                                    <option value="{{ $cluster }}">{{ $cluster }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Provinsi</label>
                                    <select class="form-select" name="provinsi_filter">
                                        <option value="">Semua Provinsi</option>
                                        @if(isset($pelanggans) && $pelanggans->count() > 0)
                                            @foreach($pelanggans->pluck('provinsi')->unique()->sort() as $provinsi)
                                                @if($provinsi)
                                                    <option value="{{ $provinsi }}">{{ $provinsi }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Kabupaten</label>
                                    <select class="form-select" name="kabupaten_filter">
                                        <option value="">Semua Kabupaten</option>
                                        @if(isset($pelanggans) && $pelanggans->count() > 0)
                                            @foreach($pelanggans->pluck('kabupaten')->unique()->sort() as $kabupaten)
                                                @if($kabupaten)
                                                    <option value="{{ $kabupaten }}">{{ $kabupaten }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Range Bandwidth</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="bandwidth_min" placeholder="Min">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="bandwidth_max" placeholder="Max">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tanggal Registrasi</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="date_from">
                                        </div>
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="date_to">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Kode FAT</label>
                                    <select class="form-select" name="has_fat_code">
                                        <option value="">Semua</option>
                                        <option value="yes">Ada FAT</option>
                                        <option value="no">Tidak Ada FAT</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Koordinat</label>
                                    <select class="form-select" name="has_coordinates">
                                        <option value="">Semua</option>
                                        <option value="yes">Ada Koordinat</option>
                                        <option value="no">Tidak Ada Koordinat</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-filter me-1"></i>Advanced Search
                                        </button>
                                        <a href="{{ route('customer.search') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    @if(isset($pelanggans) && $pelanggans->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('customer.map') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-map-marked-alt me-1"></i>Lihat di Peta
                            </a>
                        </div>
                        <small class="text-muted">
                            Menampilkan {{ $pelanggans->firstItem() ?? 0 }}-{{ $pelanggans->lastItem() ?? 0 }} dari {{ $pelanggans->total() ?? 0 }} data
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Results Table -->
