@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
  {{-- Debug: Cek apakah variabel admin ada --}}
  @if(!isset($admin))
    <div class="alert alert-danger">
      <h4>Debug Error:</h4>
      <p>Variabel $admin tidak ditemukan!</p>
      <p>Pastikan controller method edit() mengirim variabel admin dengan compact('admin')</p>
    </div>
  @endif

  <div class="card shadow border-0">
    {{-- 🔹 Header --}}
    <div class="card-header bg-gradient-danger text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-user-shield"></i> Edit Admin</h4>
      <a href="{{ route('admins.index') }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>
    </div>

    {{-- 🔹 Body --}}
    <div class="card-body">
      {{-- Hanya tampilkan form jika $admin ada --}}
      @if(isset($admin))
        <form method="POST" action="{{ route('admins.update', $admin->id) }}">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nama') }}</label>
            <div class="col-md-6">
              <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                     name="name" value="{{ old('name', $admin->name ?? '') }}" required autocomplete="name" autofocus>
              @error('name')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
            <div class="col-md-6">
              <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ old('email', $admin->email ?? '') }}" required autocomplete="email">
              @error('email')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <label for="role" class="col-md-4 col-form-label text-md-end">{{ __('Role') }}</label>
            <div class="col-md-6">
              {{-- Role admin tidak bisa diubah --}}
              <input type="text" class="form-control" value="Admin" readonly>
              <input type="hidden" name="role" value="admin">
              <small class="form-text text-muted">Role admin tidak dapat diubah</small>
            </div>
          </div>

          {{-- Info tentang password --}}
          <div class="row mb-3">
            <div class="col-md-6 offset-md-4">
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Catatan:</strong> Password tidak dapat diubah melalui form ini untuk alasan keamanan.
                Gunakan fitur reset password jika diperlukan.
              </div>
            </div>
          </div>

          <div class="row mb-0">
            <div class="col-md-6 offset-md-4">
              <button type="submit" class="btn btn-danger px-4">
                <i class="fas fa-save"></i> Update Admin
              </button>
              <a href="{{ route('admins.index') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-times"></i> Batal
              </a>
            </div>
          </div>
        </form>
      @else
        <div class="alert alert-warning">
          <p>Data admin tidak dapat dimuat. Silakan kembali ke halaman daftar admin.</p>
          <a href="{{ route('admins.index') }}" class="btn btn-danger">Kembali ke Daftar Admin</a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
