@extends('layouts.app')

@section('title', 'Tambah Admin')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user-shield"></i> Tambah Admin Baru
                        </h5>
                        <a href="{{ route('admins.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admins.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Nama Lengkap --}}
                        <div class="mb-3 row">
                            <label for="name" class="col-md-3 col-form-label text-md-end">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Masukkan nama lengkap admin"
                                       required
                                       autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3 row">
                            <label for="email" class="col-md-3 col-form-label text-md-end">
                                Email <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="admin@example.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="mb-3 row">
                            <label for="password" class="col-md-3 col-form-label text-md-end">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Minimal 8 karakter"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Password harus minimal 8 karakter</small>
                            </div>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-3 row">
                            <label for="password_confirmation" class="col-md-3 col-form-label text-md-end">
                                Konfirmasi Password <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Masukkan ulang password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye" id="eyeIconConfirm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Role (Hidden/Readonly) --}}
                        <div class="mb-3 row">
                            <label for="role_display" class="col-md-3 col-form-label text-md-end">
                                Role <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <input type="text"
                                       class="form-control"
                                       id="role_display"
                                       value="Admin"
                                       readonly>
                                <input type="hidden" name="role" value="admin">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Role otomatis diatur sebagai Admin
                                </small>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Tombol Aksi --}}
                        <div class="row">
                            <div class="col-md-8 offset-md-3">
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-save"></i> Simpan Admin
                                </button>
                                <a href="{{ route('admins.index') }}" class="btn btn-secondary px-4 ms-2">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
        const password = document.getElementById('password_confirmation');
        const icon = document.getElementById('eyeIconConfirm');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Validasi password confirmation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirm = this.value;

        if (password !== confirm) {
            this.setCustomValidity('Password tidak sama');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });

    // Reset validasi saat password utama berubah
    document.getElementById('password').addEventListener('input', function() {
        const confirmInput = document.getElementById('password_confirmation');
        if (confirmInput.value !== '') {
            confirmInput.dispatchEvent(new Event('input'));
        }
    });
</script>
@endpush
