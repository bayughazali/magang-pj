@extends('layouts.app')

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row">
                <div class="col">
                    <div class="card shadow h-100">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <h3 class="text-primary mb-0">
                                <i class="ni ni-single-02 text-primary"></i>
                                Profile Settings
                            </h3>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Alert Messages -->
                            <div id="alertContainer">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <span class="alert-icon"><i class="ni ni-check-bold"></i></span>
                                        <span class="alert-text">{{ session('success') }}</span>
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <span class="alert-icon"><i class="ni ni-support-16"></i></span>
                                        <span class="alert-text">{{ session('error') }}</span>
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <span class="alert-icon"><i class="ni ni-support-16"></i></span>
                                        <div class="alert-text">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <button type="button" class="close" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <!-- Profile Form Section -->
                                <div class="col-lg-8">
                                    <div class="card shadow-lg border-0">
                                        <div class="card-header bg-white border-0">
                                            <h3 class="mb-0">Edit Profile</h3>
                                            <p class="text-sm mb-0">Update informasi profile Anda</p>
                                        </div>
                                        <div class="card-body">
                                            <!-- Form Edit Profile -->
                                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                                                @csrf
                                                @method('PUT')

                                                <!-- Upload Photo Section -->
                                                <div class="form-group">
                                                    <label class="form-control-label">Foto Profile</label>
                                                    <div class="d-flex align-items-center">
                                                        <!-- Preview foto saat ini -->
                                                        <div class="avatar-preview mr-3" style="position: relative; width: 80px; height: 80px;">
                                                            @if(Auth::user()->profile_photo_path)
                                                                <img id="photoPreview"
                                                                    src="{{ Storage::url(Auth::user()->profile_photo_path) }}"
                                                                    class="rounded-circle"
                                                                    style="width: 80px; height: 80px; object-fit: cover; position: absolute; top: 0; left: 0;"
                                                                    onerror="this.style.display='none'; document.getElementById('defaultAvatar').style.display='flex';">
                                                            @else
                                                                <img id="photoPreview"
                                                                    src=""
                                                                    class="rounded-circle d-none"
                                                                    style="width: 80px; height: 80px; object-fit: cover; position: absolute; top: 0; left: 0;">
                                                            @endif

                                                            <div id="defaultAvatar"
                                                                class="rounded-circle d-flex align-items-center justify-content-center {{ Auth::user()->profile_photo_path ? 'd-none' : '' }}"
                                                                style="width: 80px; height: 80px; background: #f8f9fa; border: 2px dashed #dee2e6; position: absolute; top: 0; left: 0;">
                                                                <i class="fas fa-user text-muted" style="font-size: 30px;"></i>
                                                            </div>
                                                        </div>

                                                        <!-- Input file dan tombol hapus -->
                                                        <div class="flex-grow-1">
                                                            <input type="file"
                                                                class="form-control @error('profile_photo') is-invalid @enderror"
                                                                id="profile_photo"
                                                                name="profile_photo"
                                                                accept="image/*"
                                                                onchange="previewImage(this)">
                                                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                                                            @error('profile_photo')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror

                                                            <!-- Tombol hapus foto -->
                                                            @if(Auth::user()->profile_photo_path)
                                                                <div class="mt-2">
                                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                                            onclick="deleteProfilePhoto()">
                                                                        <i class="fas fa-trash"></i> Hapus Foto
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Nama Lengkap -->
                                                <div class="form-group">
                                                    <label class="form-control-label">Nama Lengkap</label>
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        name="name"
                                                        value="{{ old('name', Auth::user()->name) }}"
                                                        required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Email -->
                                                <div class="form-group">
                                                    <label class="form-control-label">Email</label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email"
                                                        value="{{ old('email', Auth::user()->email) }}"
                                                        required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Role (Read Only) -->
                                                <div class="form-group">
                                                    <label class="form-control-label">Role</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        value="{{ ucfirst(Auth::user()->role) }}"
                                                        readonly>
                                                    <small class="text-muted">Role tidak dapat diubah</small>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                                        <i class="fas fa-save mr-1"></i>
                                                        <span id="submitText">Simpan Perubahan</span>
                                                        <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none ml-2" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                              <!-- Profile Info Panel -->
<div class="col-lg-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-white border-0">
            <h3 class="mb-0">Informasi Akun</h3>
        </div>
        <div class="card-body">
            <!-- Profile Info -->
            <div class="text-center mb-4">
                <!-- Tampilkan foto profile atau default avatar, tapi tidak keduanya -->
                @if(Auth::user()->profile_photo_path)
                    <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}"
                        class="rounded-circle mb-3"
                        style="width: 120px; height: 120px; object-fit: cover;"
                        onerror="this.style.display='none'; document.getElementById('defaultAvatarInfo').style.display='flex';">
                @else
                    <div id="defaultAvatarInfo"
                        class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 120px; height: 120px; background: #f8f9fa; border: 2px dashed #dee2e6;">
                        <i class="fas fa-user text-muted" style="font-size: 40px;"></i>
                    </div>
                @endif

                <h3>{{ Auth::user()->name }}</h3>
                <span class="badge badge-{{ Auth::user()->role === 'admin' ? 'primary' : 'secondary' }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>

            <!-- Account Details -->
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ Auth::user()->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Bergabung:</strong></td>
                            <td>{{ Auth::user()->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Terakhir Login:</strong></td>
                            <td>{{ Auth::user()->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

           <!-- Change Password Section - Alternative -->
        <hr class="my-4">
        <div class="text-center">
            <h5 class="mb-3">Keamanan Akun</h5>
            <a href="{{ route('forgot.password') }}" class="btn btn-outline-warning btn-block">
                <i class="ni ni-key-25"></i>
                Ubah Password
            </a>
        </div>
        </div>
    </div>
</div>

<!-- Modal untuk konfirmasi hapus foto -->
<div class="modal fade" id="deletePhotoModal" tabindex="-1" role="dialog" aria-labelledby="deletePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePhotoModalLabel">Konfirmasi Hapus Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus foto profile?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('profile.photo.delete') }}" class="d-inline" id="deletePhotoForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        // Validasi ukuran file (2MB = 2048KB)
        const maxSize = 2048 * 1024; // 2MB in bytes
        if (input.files[0].size > maxSize) {
            alert('Ukuran file terlalu besar! Maksimal 2MB');
            input.value = '';
            return;
        }

        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(input.files[0].type)) {
            alert('Format file tidak didukung! Gunakan JPG, PNG, atau GIF');
            input.value = '';
            return;
        }

        var reader = new FileReader();

        reader.onload = function(e) {
            // Update preview di form (kiri)
            const preview = document.getElementById('photoPreview');
            const defaultAvatar = document.getElementById('defaultAvatar');

            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                preview.style.display = 'block';

                // Hide default avatar
                if (defaultAvatar) {
                    defaultAvatar.classList.add('d-none');
                }
            }

            // Update preview di info panel (kanan)
            const previewInfo = document.getElementById('photoPreviewInfo');
            const defaultAvatarInfo = document.getElementById('defaultAvatarInfo');

            if (previewInfo) {
                previewInfo.src = e.target.result;
                previewInfo.classList.remove('d-none');
                previewInfo.style.display = 'block';

                // Hide default avatar info
                if (defaultAvatarInfo) {
                    defaultAvatarInfo.classList.add('d-none');
                }
            }
        }

        reader.onerror = function() {
            alert('Gagal membaca file. Silakan coba lagi.');
            input.value = '';
        }

        reader.readAsDataURL(input.files[0]);
    }
}

function deleteProfilePhoto() {
    $('#deletePhotoModal').modal('show');
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if ($(alert).length) {
                $(alert).alert('close');
            }
        });
    }, 5000);

    // Form submission dengan loading state
    const form = document.getElementById('profileForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Disable button dan show loading
            submitBtn.disabled = true;
            submitText.textContent = 'Menyimpan...';
            loadingSpinner.classList.remove('d-none');

            // Validasi form sebelum submit
            const name = form.querySelector('input[name="name"]').value.trim();
            const email = form.querySelector('input[name="email"]').value.trim();

            if (!name || !email) {
                e.preventDefault();
                alert('Nama dan Email wajib diisi!');

                // Reset button state
                submitBtn.disabled = false;
                submitText.textContent = 'Simpan Perubahan';
                loadingSpinner.classList.add('d-none');
                return;
            }

            // Validasi email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Format email tidak valid!');

                // Reset button state
                submitBtn.disabled = false;
                submitText.textContent = 'Simpan Perubahan';
                loadingSpinner.classList.add('d-none');
                return;
            }

            // Log untuk debugging
            console.log('Form sedang disubmit...');
            console.log('Nama:', name);
            console.log('Email:', email);

            const photoInput = form.querySelector('input[name="profile_photo"]');
            if (photoInput.files.length > 0) {
                console.log('File foto:', photoInput.files[0].name);
                console.log('Ukuran foto:', photoInput.files[0].size, 'bytes');
            }
        });
    }

    // Reset form state jika terjadi error (back dari server)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error') || document.querySelector('.alert-danger')) {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitText.textContent = 'Simpan Perubahan';
            loadingSpinner.classList.add('d-none');
        }
    }
});

// Debug function untuk testing
function debugFormData() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);

    console.log('Form Data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
}
</script>

<style>
.alert {
    margin-bottom: 1rem;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.avatar-preview img {
    transition: transform 0.3s ease;
    border: 3px solid #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.avatar-preview img:hover {
    transform: scale(1.05);
}

.table td {
    padding: 0.5rem 0;
    border: none;
}

.table td:first-child {
    width: 40%;
    color: #8898aa;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
}

/* Loading state untuk button */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Improved file input styling */
.form-control[type="file"] {
    padding: 0.375rem 0.75rem;
}

.form-control[type="file"]:focus {
    border-color: #5e72e4;
    box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
}

/* Avatar preview improvements */
.avatar-preview {
    position: relative;
}

.avatar-preview::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 20px;
    height: 20px;
    background: #5e72e4;
    border-radius: 50%;
    border: 2px solid white;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.avatar-preview:hover::after {
    opacity: 1;
}
</style>
@endsection
