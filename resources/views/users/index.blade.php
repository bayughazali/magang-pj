@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
  {{-- ✅ Alert Success/Error --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
    {{-- 🔹 Header --}}
    <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #009FE3;">
      <h4 class="mb-0 fw-bold">
        <i class="fas fa-users me-2"></i> Daftar User
      </h4>
      {{-- <a href="{{ route('users.create') }}" class="btn btn-light text-primary fw-semibold shadow-sm">
        <i class="fas fa-plus"></i> Tambah User
      </a> --}}
    </div>

    {{-- 🔹 Body --}}
    <div class="card-body" style="background-color: #F6FBFF;">
      <div class="table-responsive bg-white rounded-3 shadow-sm p-3">
        <table class="table table-hover align-middle mb-0">
          <thead class="text-white text-center" style="background-color: #009FE3;">
            <tr>
              <th>#</th>
              <th>Foto Profil</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Tanggal Dibuat</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="text-center">
            @forelse ($users as $index => $user)
              <tr class="hover-row">
                <td>{{ $users->firstItem() + $index }}</td>
                <td>
                  @if($user->profile_photo_path && file_exists(storage_path('app/public/' . $user->profile_photo_path)))
                    <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Foto Profil" width="70" height="70" class="rounded-circle shadow-sm" style="object-fit: cover;">
                  @elseif($user->photo && file_exists(storage_path('app/public/photo/' . $user->photo)))
                    <img src="{{ asset('storage/photo/' . $user->photo) }}" alt="Foto Profil" width="70" height="70" class="rounded-circle shadow-sm" style="object-fit: cover;">
                  @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 70px; height: 70px; background: #E3F2FD; border: 2px solid #BBDEFB;">
                      <i class="fas fa-user text-primary" style="font-size: 25px;"></i>
                    </div>
                  @endif
                </td>
                <td class="fw-semibold text-primary">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @if($user->role == 'admin')
                    <span class="badge text-uppercase" style="background-color: #E74C3C;">{{ $user->role }}</span>
                  @else
                    <span class="badge text-uppercase" style="background-color: #009FE3;">{{ $user->role }}</span>
                  @endif
                </td>
                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <a href="{{ route('users.edit', $user->id) }}" 
                       class="btn btn-sm text-white shadow-sm" 
                       style="background-color: #F1C40F; border: none;" 
                       title="Edit User">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" 
                            class="btn btn-sm text-white shadow-sm" 
                            style="background-color: #E74C3C; border: none;"
                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" 
                            title="Hapus User">
                      <i class="fas fa-trash"></i> Hapus
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                  <p class="mb-3">Belum ada data user</p>
                  <a href="{{ route('users.create') }}" class="btn text-white fw-semibold shadow-sm" style="background-color: #009FE3;">
                    <i class="fas fa-plus"></i> Tambah User Pertama
                  </a>
                </td>
              </tr>
            @endforelse
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

{{-- Hidden Delete Form --}}
<form id="delete-form" action="" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>

{{-- JS Konfirmasi Delete --}}
<script>
function confirmDelete(userId, userName) {
  if (confirm('Apakah Anda yakin ingin menghapus user "' + userName + '"?\n\nData yang dihapus tidak dapat dikembalikan!')) {
    const form = document.getElementById('delete-form');
    form.action = '{{ route("users.destroy", ":id") }}'.replace(':id', userId);
    form.submit();
  }
}
</script>

{{-- STYLE TAMBAHAN --}}
<style>
  .hover-row:hover {
      background-color: #E3F2FD !important;
      transition: 0.2s ease;
  }
  .btn:hover {
      transform: translateY(-2px);
      opacity: 0.9;
  }
</style>
@endsection
