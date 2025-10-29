{{-- Hanya bagian tabel yang dimodifikasi --}}
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-primary">
            <tr>
                <th style="width: 50px;">No</th>
                <th>ID Pelanggan</th>
                <th>Nama</th>
                <th>Bandwidth</th>
                <th>Telepon</th>
                <th>Provinsi</th>
                <th>Kabupaten</th>
                <th>Alamat</th>
                <th>Cluster</th>
                <th>Kode FAT</th>
                <th>Koordinat</th>
                {{-- Kolom Aksi hanya muncul jika admin --}}
                @if(auth()->user()->role === 'admin')
                    <th style="width: 120px;">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($pelanggans as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $p->id_pelanggan }}</strong></td>
                    <td>{{ $p->nama_pelanggan }}</td>
                    <td><span class="badge bg-info">{{ $p->bandwidth }}</span></td>
                    <td>{{ $p->nomor_telepon }}</td>
                    <td><span class="badge bg-primary">{{ $p->provinsi ?? '-' }}</span></td>
                    <td><span class="badge bg-secondary">{{ $p->kabupaten ?? '-' }}</span></td>
                    <td>{{ Str::limit($p->alamat, 30) }}</td>
                    <td><span class="badge bg-warning text-dark">{{ $p->cluster }}</span></td>
                    <td><strong class="text-success">{{ $p->kode_fat ?: '-' }}</strong></td>
                    <td>
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $p->latitude }}, {{ $p->longitude }}
                        </small>
                    </td>

                    {{-- Kolom Aksi: Hanya muncul jika user adalah admin --}}
                    @if(auth()->user()->role === 'admin')
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="{{ route('report.operational.edit', $p->id_pelanggan) }}"
                                class="btn btn-warning btn-sm me-2"
                                title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pelanggan.destroy', $p->id_pelanggan) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()->role === 'admin' ? '12' : '11' }}" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <br>Belum ada data pelanggan
                        <br><small>Silakan input data pelanggan di form di atas</small>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
