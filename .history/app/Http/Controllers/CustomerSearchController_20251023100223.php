<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $query = Pelanggan::query();

        // Filter berdasarkan parameter jika ada
        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }

        if ($request->filled('provinsi')) {
            $query->where('provinsi', $request->provinsi);
        }

        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }

        $pelanggans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data untuk dropdown filter
        $clusters = Pelanggan::select('cluster')
            ->distinct()
            ->whereNotNull('cluster')
            ->orderBy('cluster')
            ->pluck('cluster');

        $provinsiList = Pelanggan::select('provinsi')
            ->distinct()
            ->whereNotNull('provinsi')
            ->orderBy('provinsi')
            ->pluck('provinsi');

        return view('report.operational.index', compact('pelanggans', 'clusters', 'provinsiList'));
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
            'cluster'        => 'required|string|max:100',
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
            'cluster.required' => 'Cluster wajib dipilih',
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
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();

            // Data region untuk dropdown
            $regionData = $this->getRegionData();

            return view('report.operational.edit', compact('pelanggan', 'regionData'));

        } catch (\Exception $e) {
            Log::error("Gagal membuka form edit pelanggan ID {$id}: " . $e->getMessage());

            return redirect()
                ->route('report.operational.index')
                ->withErrors(['error' => 'Data pelanggan tidak ditemukan']);
        }
    }

    /**
     * Update data pelanggan
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_pelanggan'   => 'required|string|max:100|unique:pelanggans,id_pelanggan,' . $id . ',id_pelanggan',
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'nomor_telepon'  => 'required|string|max:50',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'kode_fat'       => 'nullable|string|max:100',
            'alamat'         => 'required|string',
            'cluster'        => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi',
            'id_pelanggan.unique' => 'ID Pelanggan sudah digunakan',
            'nama_pelanggan.required' => 'Nama Pelanggan wajib diisi',
            'bandwidth.required' => 'Bandwidth wajib diisi',
            'nomor_telepon.required' => 'Nomor Telepon wajib diisi',
            'provinsi.required' => 'Provinsi wajib dipilih',
            'kabupaten.required' => 'Kabupaten wajib dipilih',
            'alamat.required' => 'Alamat wajib diisi',
            'cluster.required' => 'Cluster wajib dipilih',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
        ]);

        try {
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();
            $pelanggan->update($validated);

            Log::info("Data pelanggan {$pelanggan->nama_pelanggan} (ID: {$id}) berhasil diupdate");

            return redirect()
                ->route('report.operational.index')
                ->with('success', "Data pelanggan {$validated['nama_pelanggan']} berhasil diperbarui!");

        } catch (\Exception $e) {
            Log::error("Gagal mengupdate pelanggan ID {$id}: " . $e->getMessage());

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
        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
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
                'Cluster',
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
                    $pelanggan->cluster,
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
