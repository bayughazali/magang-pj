@extends('layouts.admin'/)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <!-- Card Header -->
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">
                                <i class="ni ni-key-25 text-warning"></i>
                                Permintaan Reset Password
                            </h3>
                        </div>
                        <div class="col text-right">
                            <form action="{{ route('admin.password-resets.update-expired') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-sync"></i> Update Status Expired
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                        <span class="alert-icon"><i class="ni ni-check-bold"></i></span>
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                        <span class="alert-icon"><i class="ni ni-notification-70"></i></span>
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card-body">
                    <!-- Filter Status -->
                    <div class="mb-4">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('admin.password-resets.index', ['status' => 'pending']) }}"
                               class="btn {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-clock"></i> Pending
                            </a>
                            <a href="{{ route('admin.password-resets.index', ['status' => 'used']) }}"
                               class="btn {{ $status === 'used' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-check"></i> Sudah Digunakan
                            </a>
                            <a href="{{ route('admin.password-resets.index', ['status' => 'expired']) }}"
                               class="btn {{ $status === 'expired' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-times-circle"></i> Expired
                            </a>
                        </div>
                    </div>

                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-items-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Kode</th>
                                        <th>Status</th>
                                        <th>Waktu Request</th>
                                        <th>Expired</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <!-- User Info -->
                                            <td>
                                                <div class="media align-items-center">
                                                    <div class="avatar rounded-circle bg-gradient-primary text-white mr-3">
                                                        <span>{{ substr($request->user->name ?? 'U', 0, 1) }}</span>
                                                    </div>
                                                    <div class="media-body">
                                                        <h6 class="mb-0 text-sm">{{ $request->user->name ?? 'Unknown' }}</h6>
                                                        <p class="text-muted text-xs mb-0">{{ $request->email }}</p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Kode -->
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="font-weight-bold text-lg" style="font-family: 'Courier New', monospace; letter-spacing: 2px;">
                                                        {{ $request->code }}
                                                    </span>
                                                    <button class="btn btn-sm btn-icon btn-link text-primary ml-2"
                                                            onclick="copyCode('{{ $request->code }}')"
                                                            title="Copy Kode">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>

                                            <!-- Status -->
                                            <td>
                                                @if($request->status === 'pending' && !$request->isExpired())
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-clock"></i> Aktif
                                                    </span>
                                                @elseif($request->status === 'pending' && $request->isExpired())
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> Expired
                                                    </span>
                                                @elseif($request->status === 'used')
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-check"></i> Digunakan
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($request->status) }}</span>
                                                @endif
                                            </td>

                                            <!-- Waktu Request -->
                                            <td>
                                                <p class="text-sm mb-0">{{ $request->created_at->format('d M Y H:i') }}</p>
                                                <p class="text-muted text-xs mb-0">{{ $request->created_at->diffForHumans() }}</p>
                                            </td>

                                            <!-- Expired -->
                                            <td>
                                                <p class="text-sm mb-0">{{ $request->expires_at->format('d M Y H:i') }}</p>
                                                @if(!$request->isExpired() && $request->status === 'pending')
                                                    <p class="text-success text-xs mb-0">
                                                        <i class="fas fa-hourglass-half"></i> {{ $request->expires_at->diffForHumans() }}
                                                    </p>
                                                @else
                                                    <p class="text-danger text-xs mb-0">
                                                        <i class="fas fa-times-circle"></i> Sudah lewat
                                                    </p>
                                                @endif
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-right">
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                        @if($request->status === 'pending')
                                                            <!-- Generate Ulang -->
                                                            <form action="{{ route('admin.password-resets.regenerate', $request->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-sync text-info"></i> Generate Ulang Kode
                                                                </button>
                                                            </form>

                                                            <!-- Batalkan -->
                                                            <form action="{{ route('admin.password-resets.cancel', $request->id) }}" method="POST"
                                                                  onsubmit="return confirm('Yakin ingin membatalkan request ini?')">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-ban text-warning"></i> Batalkan
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <div class="dropdown-divider"></div>

                                                        <!-- Hapus -->
                                                        <form action="{{ route('admin.password-resets.destroy', $request->id) }}" method="POST"
                                                              onsubmit="return confirm('Yakin ingin menghapus request ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-inbox fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted">Tidak Ada Data</h4>
                            <p class="text-muted">Tidak ada request reset password dengan status <strong>{{ $status }}</strong></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Copy Kode -->
<script>
function copyCode(code) {
    // Buat element temporary
    const tempInput = document.createElement('input');
    tempInput.value = code;
    document.body.appendChild(tempInput);

    // Select dan copy
    tempInput.select();
    document.execCommand('copy');

    // Hapus element
    document.body.removeChild(tempInput);

    // Tampilkan notifikasi
    alert('Kode ' + code + ' berhasil disalin!\n\nSekarang Anda bisa memberikan kode ini ke user via WhatsApp/Telegram.');
}
</script>

<style>
.avatar {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.btn-icon-only {
    width: 2rem;
    height: 2rem;
    padding: 0;
}

.dropdown-menu-arrow:before {
    content: "";
    position: absolute;
    top: -8px;
    right: 20px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid #fff;
}
</style>
@endsection
