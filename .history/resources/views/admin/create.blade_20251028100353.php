@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
  {{-- Success/Error Messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card shadow border-0">
    {{-- 🔹 Header --}}
    <div class="card-header bg-gradient-danger text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-user-shield"></i> Daftar Admin</h4>
      <a href="{{ route('admins.create') }}" class="btn btn-light">
        <i class="fas fa-plus"></i> Tambah Admin
      </a>
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
            @forelse ($admins as $index => $admin)
              <tr>
                <td>{{ $admins->firstItem() + $index }}</td>
                <td>
                  @if($admin->profile_photo_path && file_exists(storage_path('app/public/' . $admin->profile_photo_path)))
                    <img src="{{ Storage::url($admin->profile_photo_path) }}"
                         alt="Foto Profil"
                         width="70"
                         height="70"
                         class="rounded-circle"
                         style="object-fit: cover;">
                  @elseif($admin->photo && file_exists(storage_path('app/public/photo/' . $admin->photo)))
                    <img src="{{ asset('storage/photo/' . $admin->photo) }}"
                         alt="Foto Profil"
                         width="70"
                         height="70"
                         class="rounded-circle"
                         style="object-fit: cover;">
                  @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 70px; height: 70px; background: #ffe5e5; border: 2px solid #dc3545;">
                      <i class="fas fa-user-shield text-danger" style="font-size: 25px;"></i>
                    </div>
                  @endif
                </td>
                <td><strong>{{ $admin->name }}</strong></td>
                <td>{{ $admin->email }}</td>
                <td>
                  <span class="badge bg-danger text-uppercase">{{ $admin->role }}</span>
                </td>
                <td>{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    {{-- Tombol Edit --}}
                    <a href="{{ route('admins.edit', $admin->id) }}"
                       class="btn btn-sm btn-warning"
                       title="Edit Admin">
                      <i class="fas fa-edit"></i> Edit
                    </a>

                    {{-- Tombol Hapus --}}
                    @if(auth()->id() != $admin->id)
                      <button type="button"
                              class="btn btn-sm btn-danger"
                              title="Hapus Admin"
                              onclick="confirmDelete({{ $admin->id }}, '{{ $admin->name }}')">
                        <i class="fas fa-trash"></i> Hapus
                      </button>
                    @else
                      <button type="button"
                              class="btn btn-sm btn-secondary"
                              title="Tidak dapat menghapus diri sendiri"
                              disabled>
                        <i class="fas fa-ban"></i> Hapus
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4">
                  <div class="text-muted">
                    <i class="fas fa-user-shield fa-3x mb-3"></i>
                    <p>Belum ada data admin</p>
                    <a href="{{ route('admins.create') }}" class="btn btn-danger">
                      <i class="fas fa-plus"></i> Tambah Admin Pertama
                    </a>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($admins->hasPages())
        <div class="d-flex justify-content-center mt-4">
          {{ $admins->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Form Hidden untuk Delete --}}
<form id="delete-form" action="" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>

{{-- JavaScript untuk Konfirmasi Delete --}}
<script>
function confirmDelete(adminId, adminName) {
  if (confirm('Apakah Anda yakin ingin menghapus admin "' + adminName + '"?\n\nData yang dihapus tidak dapat dikembalikan!')) {
    const form = document.getElementById('delete-form');
    form.action = '{{ route("admins.destroy", ":id") }}'.replace(':id', adminId);
    form.submit();
  }
}
</script>
@endsection
