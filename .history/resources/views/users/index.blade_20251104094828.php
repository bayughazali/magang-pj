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
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-users"></i> Daftar User</h4>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('users.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah User
            </a>
        @endif
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
            @forelse ($users as $index => $user)
              <tr>
                <td>{{ $users->firstItem() + $index }}</td>
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
                    {{-- Tombol Show --}}
                    <!-- <a href="{{ route('users.show', $user->id) }}"
                       class="btn btn-sm btn-info text-white"
                       title="Lihat Detail">
                      <i class="fas fa-eye"></i> Lihat
                    </a> -->

                    {{-- Tombol Edit --}}
                    <a href="{{ route('users.edit', $user->id) }}"
                    class="btn btn-sm btn-warning"
                    title="Edit User"
                    style="margin-right: 8px;">
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
            @empty
              <tr>
                <td colspan="7" class="text-center py-4">
                  <div class="text-muted">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <p>Belum ada data user</p>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                      <i class="fas fa-plus"></i> Tambah User Pertama
                    </a>
                  </div>
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

    {{-- Form Hidden untuk Delete --}}
    <form id="delete-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    </form>

{{-- JavaScript untuk Konfirmasi Delete --}}
<script>
function confirmDelete(userId, userName) {
  if (confirm('Apakah Anda yakin ingin menghapus user "' + userName + '"?\n\nData yang dihapus tidak dapat dikembalikan!')) {
    const form = document.getElementById('delete-form');
    form.action = '{{ route("users.destroy", ":id") }}'.replace(':id', userId);
    form.submit();
  }
}
</script>
@endsection
