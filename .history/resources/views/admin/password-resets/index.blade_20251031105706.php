@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6>Permintaan Reset Password</h6>
                        <form action="{{ route('admin.password-resets.update-expired') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-sync"></i> Update Status Expired
                            </button>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card-body px-0 pt-0 pb-2">
                    <!-- Filter Status -->
                    <div class="px-4 py-3">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.password-resets.index', ['status' => 'pending']) }}"
                               class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                                Pending
                            </a>
                            <a href="{{ route('admin.password-resets.index', ['status' => 'used']) }}"
                               class="btn btn-sm {{ $status === 'used' ? 'btn-success' : 'btn-outline-success' }}">
                                Sudah Digunakan
                            </a>
                            <a href="{{ route('admin.password-resets.index', ['status' => 'expired']) }}"
                               class="btn btn-sm {{ $status === 'expired' ? 'btn-warning' : 'btn-outline-warning' }}">
                                Expired
                            </a>
                        </div>
                    </div>

                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Waktu Request</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Expired</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $request->user->name ?? 'Unknown' }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $request->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-lg font-weight-bold mb-0 font-monospace">
                                                    {{ $request->code }}
                                                </p>
                                            </td>
                                            <td>
                                                @if($request->status === 'pending' && !$request->isExpired())
                                                    <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                                @elseif($request->status === 'pending' && $request->isExpired())
                                                    <span class="badge badge-sm bg-gradient-warning">Expired</span>
                                                @elseif($request->status === 'used')
                                                    <span class="badge badge-sm bg-gradient-info">Digunakan</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-secondary">{{ ucfirst($request->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <p class="text-xs mb-0">{{ $request->created_at->format('d M Y H:i') }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ $request->created_at->diffForHumans() }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs mb-0">{{ $request->expires_at->format('d M Y H:i') }}</p>
                                                @if(!$request->isExpired() && $request->status === 'pending')
                                                    <p class="text-xs text-success mb-0">{{ $request->expires_at->diffForHumans() }}</p>
                                                @else
                                                    <p class="text-xs text-danger mb-0">Sudah lewat</p>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <div class="dropdown">
                                                    <button class="btn btn-link text-secondary mb-0" type="button" data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v text-xs"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if($request->status === 'pending')
                                                            <li>
                                                                <form action="{{ route('admin.password-resets.regenerate', $request->id) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-sync me-2"></i> Generate Ulang Kode
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('admin.password-resets.cancel', $request->id) }}" method="POST"
                                                                      onsubmit="return confirm('Yakin ingin membatalkan request ini?')">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-warning">
                                                                        <i class="fas fa-ban me-2"></i> Batalkan
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <form action="{{ route('admin.password-resets.destroy', $request->id) }}" method="POST"
                                                                  onsubmit="return confirm('Yakin ingin menghapus request ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-2"></i> Hapus
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="px-4 py-3">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-secondary mb-3"></i>
                            <p class="text-secondary">Tidak ada request reset password dengan status {{ $status }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
