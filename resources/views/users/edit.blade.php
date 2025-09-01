@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
  {{-- Debug: Cek apakah variabel user ada --}}
  @if(!isset($user))
    <div class="alert alert-danger">
      <h4>Debug Error:</h4>
      <p>Variabel $user tidak ditemukan!</p>
      <p>Pastikan controller method edit() mengirim variabel user dengan compact('user')</p>
    </div>
  @endif

  <div class="card shadow border-0">
    {{-- 🔹 Header --}}
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-user-edit"></i> Edit User</h4>
      <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>
    </div>

    {{-- 🔹 Body --}}
    <div class="card-body">
      {{-- Hanya tampilkan form jika $user ada --}}
      @if(isset($user))
        <form method="POST" action="{{ route('users.update', $user->id) }}">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nama') }}</label>
            <div class="col-md-6">
              <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                     name="name" value="{{ old('name', $user->name ?? '') }}" required autocomplete="name" autofocus>
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
                     name="email" value="{{ old('email', $user->email ?? '') }}" required autocomplete="email">
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
              @if(auth()->user()->role === 'admin')
                {{-- Hanya admin yang bisa mengubah role --}}
                <select id="role" class="form-control @error('role') is-invalid @enderror" name="role" required>
                  <option value="">Pilih Role</option>
                  <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                  <option value="user" {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>User</option>
                </select>
                @error('role')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              @else
                {{-- User biasa hanya bisa melihat role, tidak bisa mengubah --}}
                <input type="text" class="form-control" value="{{ ucfirst($user->role ?? '') }}" readonly>
                <input type="hidden" name="role" value="{{ $user->role }}">
                <small class="form-text text-muted">Anda tidak memiliki izin untuk mengubah role user</small>
              @endif
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
              <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save"></i> Update User
              </button>
              <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-times"></i> Batal
              </a>
            </div>
          </div>
        </form>
      @else
        <div class="alert alert-warning">
          <p>Data user tidak dapat dimuat. Silakan kembali ke halaman daftar user.</p>
          <a href="{{ route('users.index') }}" class="btn btn-primary">Kembali ke Daftar User</a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection