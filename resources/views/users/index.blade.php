@extends('layouts.app')

@section('content')
<<<<<<< HEAD
<div class="container-fluid mt-4">
  <div class="card shadow border-0">
    {{-- 🔹 Header --}}
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-users"></i> Daftar User</h4>
      <!-- <!-- <a href="{{ route('users.create') }}" class="btn btn-success">
         <i class="fas fa-plus"></i> Tambah User -->
      </a>  
=======
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-primary">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-white mb-0">
                                <i class="ni ni-single-02 mr-2"></i>Daftar User
                            </h6>
                        </div>
                        {{-- <div class="col text-right">
                            <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus mr-1"></i>Tambah User
                            </a>
                        </div> --}}
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Tanggal Dibuat</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="media align-items-center">
                                            <div class="avatar rounded-circle mr-3 bg-info text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ni ni-single-02"></i>
                                            </div>
                                            <div class="media-body">
                                                <span class="mb-0 text-sm font-weight-bold">{{ $user->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->role == 'admin' ? 'success' : 'info' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-secondary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <!-- <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                                    <i class="fas fa-eye mr-2"></i>Lihat
                                                </a> -->
                                                <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                                    <i class="fas fa-edit mr-2"></i>Edit
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}?')">
                                                        <i class="fas fa-trash mr-2"></i>Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ni ni-single-02" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="mt-2 mb-3">Belum ada user yang terdaftar</p>
                                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus mr-1"></i>Tambah User Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($users) && $users->hasPages())
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-sm text-muted mb-0">
                                    Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} user
                                </p>
                            </div>
                            <div class="col-md-6">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
    </div>

    {{-- 🔹 Body --}}
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Foto Profil</th>
              <th scope="col">Nama</th>
              <th scope="col">Email</th>
              <th scope="col">Role</th>
              <th scope="col">Tanggal Dibuat</th>
              <th scope="col" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody style="background-color: #ffffff;">
            @foreach ($users as $index => $user)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                  @if($user->profile_photo_path && file_exists(storage_path('app/public/' . $user->profile_photo_path)))
                    <img src="{{ Storage::url($user->profile_photo_path) }}" 
                         alt="Foto Profil" 
                         width="70" 
                         height="70" 
                         class="rounded-circle" 
                         style="object-fit: cover;">
                  @elseif($user->photo && file_exists(storage_path('app/public/photo/' . $user->photo)))
                    <img src="{{ asset('storage/photo/' . $user->photo) }}" 
                         alt="Foto Profil" 
                         width="70" 
                         height="70" 
                         class="rounded-circle" 
                         style="object-fit: cover;">
                  @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 70px; height: 70px; background: #f8f9fa; border: 2px solid #dee2e6;">
                      <i class="fas fa-user text-muted" style="font-size: 25px;"></i>
                    </div>
                  @endif
                </td>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>
                  @if($user->role == 'admin')
                    <span class="badge bg-danger text-uppercase">{{ $user->role }}</span>
                  @else
                    <span class="badge bg-info text-uppercase">{{ $user->role }}</span>
                  @endif
                </td>
                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    {{-- Tombol Edit --}}
                    <a href="{{ route('users.edit', $user->id) }}" 
                       class="btn btn-sm btn-warning" 
                       title="Edit User">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    
                    {{-- Tombol Hapus --}}
                    <button type="button" 
                            class="btn btn-sm btn-danger" 
                            title="Hapus User"
                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                      <i class="fas fa-trash"></i> Hapus
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
            
            @if($users->count() == 0)
              <tr>
                <td colspan="7" class="text-center py-4">
                  <div class="text-muted">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <p>Belum ada data user</p>
                  </div>
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
      
      {{-- Pagination --}}
      @if($users->hasPages())
        <div class="d-flex justify-content-center mt-4">
          {{ $users->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
<<<<<<< HEAD

{{-- Form Hidden untuk Delete --}}
<form id="delete-form" action="" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>

{{-- JavaScript untuk Konfirmasi Delete --}}
<script>
function confirmDelete(userId, userName) {
  if (confirm('Apakah Anda yakin ingin menghapus user "' + userName + '"?')) {
    const form = document.getElementById('delete-form');
    form.action = '{{ route("users.destroy", ":id") }}'.replace(':id', userId);
    form.submit();
  }
}
</script>

{{-- Success/Error Messages --}}
@if(session('success'))
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
@endif

@if(session('error'))
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
@endif
@endsection
=======
@endsection
>>>>>>> ae171d0e20c91b17be4560c4cb10c5e772cf2184
