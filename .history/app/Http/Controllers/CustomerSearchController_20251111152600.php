<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CustomerSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman operational report
     */
   public function index(Request $request)
{
    // 🔹 1. Buat query awal
    $query = \App\Models\Pelanggan::query();

    // 🔹 2. Jika ada input pencarian (tetap difilter)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('id_pelanggan', 'like', "%{$search}%")
              ->orWhere('nama_pelanggan', 'like', "%{$search}%")
              ->orWhere('nomor_telepon', 'like', "%{$search}%")
              ->orWhere('kode_fat', 'like', "%{$search}%");
        });
    }

    // 🔹 3. Jika tidak ada pencarian sama sekali, tampilkan semua data pelanggan
    //     Inilah bagian penting agar tabel muncul otomatis
    $pelanggans = $query->orderBy('created_at', 'desc')->paginate(15);

    // 🔹 4. Data tambahan dropdown (optional)
    $clusters = \App\Models\Pelanggan::select('cluster')->distinct()->pluck('cluster');
    $provinsiList = \App\Models\Pelanggan::select('provinsi')->distinct()->pluck('provinsi');

// Filter berdasarkan Provinsi
    if ($request->filled('provinsi')) {
        $query->where('provinsi', strtoupper($request->provinsi));
    }

    // Filter berdasarkan Cluster
    if ($request->filled('kecamatan')) {
        $query->where('cluster', strtoupper($request->cluster));
    }

    // Ambil data hasil pencarian
    $pelanggans = $query->paginate(10);

    // Data dropdown
    $provinsiList = ['BALI', 'NUSA TENGGARA BARAT', 'NUSA TENGGARA TIMUR'];
    $clusters = ['CLUSTER A', 'CLUSTER B', 'CLUSTER C', 'CLUSTER D'];


    // kirim ke view
    return view('report.customer.search', compact('pelanggans', 'provinsiList', 'clusters'));
}

    /**
     * Simpan data pelanggan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan'   => 'required|string|max:100|unique:pelanggans,id_pelanggan',
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'nomor_telepon'  => 'required|string|max:50',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'kode_fat'       => 'nullable|string|max:100',
            'alamat'         => 'required|string',
            'kecamatan'      => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi',
            'id_pelanggan.unique' => 'ID Pelanggan sudah terdaftar',
            'nama_pelanggan.required' => 'Nama Pelanggan wajib diisi',
            'bandwidth.required' => 'Bandwidth wajib diisi',
            'nomor_telepon.required' => 'Nomor Telepon wajib diisi',
            'provinsi.required' => 'Provinsi wajib dipilih',
            'kabupaten.required' => 'Kabupaten wajib dipilih',
            'alamat.required' => 'Alamat wajib diisi',
            'kecamatan.required' => 'Kecamatan wajib dipilih',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
        ]);

        try {
            Pelanggan::create($validated);

            Log::info("Pelanggan baru berhasil ditambahkan: {$validated['nama_pelanggan']} (ID: {$validated['id_pelanggan']})");

            return redirect()
                ->route('report.operational.index')
                ->with('success', "Data pelanggan {$validated['nama_pelanggan']} berhasil ditambahkan!");

        } catch (\Exception $e) {
            Log::error("Gagal menambahkan pelanggan: " . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilkan form edit pelanggan
     */
     public function edit($id)
    {
         try {
            // Cari berdasarkan id_pelanggan
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();

            // Data region untuk dropdown
            $regionData = $this->getRegionData();

            Log::info("Form edit dibuka untuk pelanggan: {$pelanggan->nama_pelanggan} (ID: {$id})");

            return view('report.operational.edit', compact('pelanggan', 'regionData'));

        } catch (\Exception $e) {
            Log::error("Gagal membuka form edit pelanggan ID {$id}: " . $e->getMessage());

            return redirect()
                ->route('report.operational.index')
                ->withErrors(['error' => 'Data pelanggan tidak ditemukan. ID: ' . $id]);
        }
    }

    /**
     * Update data pelanggan
     */
    public function update(Request $request, $id)
    {
        // Log untuk debugging
        Log::info("Attempting to update pelanggan with ID: {$id}");
        Log::info("Request data: " . json_encode($request->all()));

        // Validasi input
        $validated = $request->validate([
            'id_pelanggan'   => [
                'required',
                'string',
                'max:100',
                // Unique validation yang benar untuk primary key non-increment
                \Illuminate\Validation\Rule::unique('pelanggans', 'id_pelanggan')->ignore($id, 'id_pelanggan')
            ],
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'nomor_telepon'  => 'required|string|max:50',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'kode_fat'       => 'nullable|string|max:100',
            'alamat'         => 'required|string',
            'kecamatan'        => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi',
            'id_pelanggan.unique' => 'ID Pelanggan sudah digunakan oleh pelanggan lain',
            'nama_pelanggan.required' => 'Nama Pelanggan wajib diisi',
            'bandwidth.required' => 'Bandwidth wajib diisi',
            'nomor_telepon.required' => 'Nomor Telepon wajib diisi',
            'provinsi.required' => 'Provinsi wajib dipilih',
            'kabupaten.required' => 'Kabupaten wajib dipilih',
            'alamat.required' => 'Alamat wajib diisi',
            'kecamatan.required' => 'Kecamatan wajib dipilih',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
        ]);

        try {
            // Cari pelanggan berdasarkan id_pelanggan
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();

            // Jika ID pelanggan diubah, perlu update primary key
            if ($validated['id_pelanggan'] !== $id) {
                // Cek apakah ID baru sudah ada
                $exists = Pelanggan::where('id_pelanggan', $validated['id_pelanggan'])->exists();
                if ($exists) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['id_pelanggan' => 'ID Pelanggan sudah digunakan']);
                }

                // Hapus data lama dan buat baru dengan ID baru
                DB::transaction(function () use ($pelanggan, $validated) {
                    $oldId = $pelanggan->id_pelanggan;
                    $pelanggan->delete();
                    Pelanggan::create($validated);
                    Log::info("ID Pelanggan berhasil diubah dari {$oldId} ke {$validated['id_pelanggan']}");
                });
            } else {
                // Update biasa jika ID tidak berubah
                $pelanggan->update($validated);
            }

            Log::info("Data pelanggan berhasil diupdate: {$validated['nama_pelanggan']} (ID: {$validated['id_pelanggan']})");

            return redirect()
                ->route('report.operational.index')
                ->with('success', "Data pelanggan {$validated['nama_pelanggan']} berhasil diperbarui!");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Pelanggan tidak ditemukan dengan ID: {$id}");

            return redirect()
                ->route('report.operational.index')
                ->withErrors(['error' => 'Data pelanggan tidak ditemukan']);

        } catch (\Exception $e) {
            Log::error("Gagal mengupdate pelanggan ID {$id}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus data pelanggan
     */
    public function destroy($id)
    {
        Log::info('Attempting to delete pelanggan with ID: ' . $id);

        try {
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();
            $namaPelanggan = $pelanggan->nama_pelanggan;

            $pelanggan->delete();

            Log::info("Pelanggan {$namaPelanggan} (ID: {$id}) berhasil dihapus");

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data pelanggan {$namaPelanggan} berhasil dihapus"
                ]);
            }

            return redirect()
                ->route('report.operational.index')
                ->with('success', "Data pelanggan {$namaPelanggan} berhasil dihapus");

        } catch (\Exception $e) {
            Log::error("Delete failed for ID {$id}: " . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Export data pelanggan
     */
    public function export(Request $request)
    {
        $query = Pelanggan::query();

        // Apply filter seperti di index
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        if ($request->filled('provinsi')) {
            $query->where('provinsi', $request->provinsi);
        }

        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }

        $pelanggans = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'operational_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($pelanggans) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'ID Pelanggan',
                'Nama Pelanggan',
                'Bandwidth',
                'Nomor Telepon',
                'Provinsi',
                'Kabupaten',
                'Alamat',
                'Kecamatan',
                'Kode FAT',
                'Latitude',
                'Longitude',
                'Tanggal Dibuat'
            ]);

            // Data rows
            foreach ($pelanggans as $pelanggan) {
                fputcsv($file, [
                    $pelanggan->id_pelanggan,
                    $pelanggan->nama_pelanggan,
                    $pelanggan->bandwidth,
                    $pelanggan->nomor_telepon,
                    $pelanggan->provinsi,
                    $pelanggan->kabupaten,
                    $pelanggan->alamat,
                    $pelanggan->kecamatan,
                    $pelanggan->kode_fat,
                    $pelanggan->latitude,
                    $pelanggan->longitude,
                    $pelanggan->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper: Get data region untuk dropdown
     */
    private function getRegionData()
    {
        return [
            'Bali' => [
                'Badung', 'Denpasar', 'Gianyar', 'Tabanan',
                'Klungkung', 'Bangli', 'Karangasem', 'Buleleng', 'Jembrana'
            ],
            'Nusa Tenggara Barat' => [
                'Lombok Barat', 'Lombok Tengah', 'Lombok Timur',
                'Mataram', 'Dompu', 'Bima', 'Sumbawa', 'Sumbawa Barat',
                'Lombok Utara'
            ],
            'Nusa Tenggara Timur' => [
                'Kupang', 'Ende', 'Flores Timur', 'Sumba Barat',
                'Sumba Timur', 'Timor Tengah Selatan', 'Timor Tengah Utara',
                'Alor', 'Lembata', 'Manggarai', 'Ngada', 'Sikka'
            ],
        ];
    }

    /**
     * Get kabupaten berdasarkan provinsi (AJAX)
     */
    public function getKabupatenByProvinsi(Request $request)
    {
        $provinsi = $request->get('provinsi');
        $regionData = $this->getRegionData();

        $kabupaten = $regionData[$provinsi] ?? [];

        return response()->json($kabupaten);
    }
}
