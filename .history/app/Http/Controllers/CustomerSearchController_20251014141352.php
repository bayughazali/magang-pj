<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CustomerSearchController extends Controller
{
    /**
     * Tampilkan halaman pencarian pelanggan & kode FAT
     */
    public function index(Request $request)
    {
        // Pastikan variable selalu ada, bahkan jika kosong
        $filterField = $request->get('filter_field', '');
        $filterQuery = $request->get('filter_query', '');

        // Query builder untuk pencarian
        $query = Pelanggan::query();

        // Apply filter berdasarkan field dan query
        if ($filterField && $filterQuery) {
            switch ($filterField) {
                case 'id_pelanggan':
                    $query->where('id_pelanggan', 'like', "%{$filterQuery}%");
                    break;
                case 'nama_pelanggan':
                    $query->where('nama_pelanggan', 'like', "%{$filterQuery}%");
                    break;
                case 'bandwidth':
                    $query->where('bandwidth', 'like', "%{$filterQuery}%");
                    break;
                case 'alamat':
                    $query->where('alamat', 'like', "%{$filterQuery}%");
                    break;
                case 'provinsi':
                    $query->where('provinsi', 'like', "%{$filterQuery}%");
                    break;
                case 'kabupaten':
                    $query->where('kabupaten', 'like', "%{$filterQuery}%");
                    break;
                case 'nomor_telepon':
                    $query->where('nomor_telepon', 'like', "%{$filterQuery}%");
                    break;
                case 'cluster':
                    $query->where('cluster', 'like', "%{$filterQuery}%");
                    break;
                case 'kode_fat':
                    $query->where('kode_fat', 'like', "%{$filterQuery}%");
                    break;
                case 'latitude':
                    $query->where('latitude', 'like', "%{$filterQuery}%");
                    break;
                case 'longitude':
                    $query->where('longitude', 'like', "%{$filterQuery}%");
                    break;
                default:
                    // Search di semua field jika tidak spesifik
                    $query->where(function($q) use ($filterQuery) {
                        $q->where('id_pelanggan', 'like', "%{$filterQuery}%")
                          ->orWhere('nama_pelanggan', 'like', "%{$filterQuery}%")
                          ->orWhere('bandwidth', 'like', "%{$filterQuery}%")
                          ->orWhere('alamat', 'like', "%{$filterQuery}%")
                          ->orWhere('provinsi', 'like', "%{$filterQuery}%")
                          ->orWhere('kabupaten', 'like', "%{$filterQuery}%")
                          ->orWhere('nomor_telepon', 'like', "%{$filterQuery}%")
                          ->orWhere('cluster', 'like', "%{$filterQuery}%")
                          ->orWhere('kode_fat', 'like', "%{$filterQuery}%");
                    });
                    break;
            }
        } elseif ($filterQuery && !$filterField) {
            // Search di semua field jika hanya ada query tanpa field spesifik
            $query->where(function($q) use ($filterQuery) {
                $q->where('id_pelanggan', 'like', "%{$filterQuery}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$filterQuery}%")
                  ->orWhere('bandwidth', 'like', "%{$filterQuery}%")
                  ->orWhere('alamat', 'like', "%{$filterQuery}%")
                  ->orWhere('provinsi', 'like', "%{$filterQuery}%")
                  ->orWhere('kabupaten', 'like', "%{$filterQuery}%")
                  ->orWhere('nomor_telepon', 'like', "%{$filterQuery}%")
                  ->orWhere('cluster', 'like', "%{$filterQuery}%")
                  ->orWhere('kode_fat', 'like', "%{$filterQuery}%");
            });
        }

        // Order by latest dan paginate
        $pelanggans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('report.customer.search', compact('pelanggans', 'filterField', 'filterQuery'));
    }

    /**
     * Tampilkan form edit pelanggan untuk modal
     */
    public function edit($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            if (request()->ajax()) {
                return view('report.customer.edit-form', compact('pelanggan'))->render();
            }

            return view('report.customer.edit-form', compact('pelanggan'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Data pelanggan tidak ditemukan'], 404);
            }
            return redirect()->back()->withErrors(['error' => 'Data pelanggan tidak ditemukan']);
        }
    }

    /**
     * Update data pelanggan dari halaman pencarian
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_pelanggan'   => 'required|string|max:100|unique:pelanggans,id_pelanggan,' . $id,
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'alamat'         => 'required|string',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'nomor_telepon'  => 'required|string|max:50',
            'cluster'        => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'kode_fat'       => 'nullable|string|max:100',
        ], [
            'id_pelanggan.required' => 'ID Pelanggan wajib diisi',
            'id_pelanggan.unique' => 'ID Pelanggan sudah digunakan oleh pelanggan lain',
            'nama_pelanggan.required' => 'Nama Pelanggan wajib diisi',
            'bandwidth.required' => 'Bandwidth wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'provinsi.required' => 'Provinsi wajib dipilih',
            'kabupaten.required' => 'Kabupaten wajib dipilih',
            'nomor_telepon.required' => 'Nomor Telepon wajib diisi',
            'cluster.required' => 'Cluster wajib dipilih',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
        ]);

        try {
            $pelanggan = Pelanggan::findOrFail($id);
            $pelanggan->update($validated);

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => "Data pelanggan {$validated['nama_pelanggan']} berhasil diperbarui!"]);
            }

            // Redirect ke halaman report/customer/search dengan mempertahankan filter jika ada
            return redirect()
                ->route('customer.search', [
                    'filter_field' => $request->get('filter_field'),
                    'filter_query' => $request->get('filter_query')
                ])
                ->with('success', "Data pelanggan {$validated['nama_pelanggan']} berhasil diperbarui!");

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()], 500);
            }

            // Jika error, tetap redirect ke halaman search
            return redirect()
                ->route('customer.search')
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus data pelanggan dari halaman pencarian
     * FIXED: Method destroy yang akan dipanggil oleh JavaScript
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);

        try {
            \Log::info('Delete request received for ID: ' . $id);

            $pelanggan = Pelanggan::findOrFail($id);
            $nama = $pelanggan->nama_pelanggan;

            $pelanggan->delete();

            \Log::info('Customer deleted successfully: ' . $nama);

            // Return JSON response untuk AJAX request
            return response()->json([
                'success' => true,
                'message' => "Data pelanggan {$nama} berhasil dihapus!"
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Customer not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error deleting customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan pelanggan di peta
     */
    public function showMap(Request $request)
{
    // Ambil data pelanggan yang memiliki koordinat GPS yang valid
    $pelanggans = Pelanggan::whereNotNull('latitude')
                           ->whereNotNull('longitude')
                           ->where('latitude', '!=', 0)
                           ->where('longitude', '!=', 0)
                           ->where('latitude', '!=', '')
                           ->where('longitude', '!=', '')
                           ->where('latitude', '!=', '0')
                           ->where('longitude', '!=', '0')
                           ->get();

    // Log untuk debugging
    \Log::info('Map data loaded: ' . $pelanggans->count() . ' customers with valid coordinates');

    return view('report.customer.map', compact('pelanggans'));
}
    /**
     * Get detail pelanggan untuk modal (AJAX)
     */
    public function getDetail($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            $html = view('report.customer.detail-modal', compact('pelanggan'))->render();

            if (request()->ajax()) {
                return response()->json(['html' => $html]);
            }

            return $html;
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }
            return response('Data tidak ditemukan', 404);
        }
    }

    /**
     * Pencarian berdasarkan kode FAT saja
     */
    public function searchByFAT(Request $request)
    {
        $kodeFAT = $request->get('kode_fat', '');
        $filterField = 'kode_fat';
        $filterQuery = $kodeFAT;

        $query = Pelanggan::query();

        if ($kodeFAT) {
            $query->where('kode_fat', 'like', "%{$kodeFAT}%");
        }

        $pelanggans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('report.customer.search', compact('pelanggans', 'filterField', 'filterQuery'));
    }

    /**
     * Advanced search dengan multiple filters
     */
    public function advancedSearch(Request $request)
    {
        $query = Pelanggan::query();

        // Set default values
        $filterField = '';
        $filterQuery = '';
        $isAdvancedSearch = true;

        // Filter berdasarkan cluster
        if ($request->filled('cluster_filter')) {
            $query->where('cluster', $request->cluster_filter);
        }

        // Filter berdasarkan provinsi
        if ($request->filled('provinsi_filter')) {
            $query->where('provinsi', $request->provinsi_filter);
        }

        // Filter berdasarkan kabupaten
        if ($request->filled('kabupaten_filter')) {
            $query->where('kabupaten', $request->kabupaten_filter);
        }

        // Filter berdasarkan range bandwidth
        if ($request->filled('bandwidth_min')) {
            $query->whereRaw('CAST(REGEXP_REPLACE(bandwidth, "[^0-9]", "") AS UNSIGNED) >= ?', [$request->bandwidth_min]);
        }

        if ($request->filled('bandwidth_max')) {
            $query->whereRaw('CAST(REGEXP_REPLACE(bandwidth, "[^0-9]", "") AS UNSIGNED) <= ?', [$request->bandwidth_max]);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter berdasarkan keberadaan kode FAT
        if ($request->filled('has_fat_code')) {
            if ($request->has_fat_code == 'yes') {
                $query->whereNotNull('kode_fat')->where('kode_fat', '!=', '');
            } else {
                $query->where(function($q) {
                    $q->whereNull('kode_fat')->orWhere('kode_fat', '');
                });
            }
        }

        // Filter berdasarkan keberadaan koordinat
        if ($request->filled('has_coordinates')) {
            if ($request->has_coordinates == 'yes') {
                $query->whereNotNull('latitude')->whereNotNull('longitude');
            } else {
                $query->where(function($q) {
                    $q->whereNull('latitude')->orWhereNull('longitude');
                });
            }
        }

        $pelanggans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('report.customer.search', compact('pelanggans', 'filterField', 'filterQuery', 'isAdvancedSearch'));
    }

    /**
     * Get data untuk populate dropdown provinsi
     */
    public function getProvinsi()
    {
        $provinsi = Pelanggan::select('provinsi')
            ->distinct()
            ->whereNotNull('provinsi')
            ->where('provinsi', '!=', '')
            ->orderBy('provinsi')
            ->get();

        return response()->json($provinsi);
    }

    /**
     * Get data kabupaten berdasarkan provinsi
     */
    public function getKabupaten(Request $request)
    {
        $provinsi = $request->get('provinsi');

        $kabupaten = Pelanggan::select('kabupaten')
            ->distinct()
            ->where('provinsi', $provinsi)
            ->whereNotNull('kabupaten')
            ->where('kabupaten', '!=', '')
            ->orderBy('kabupaten')
            ->get();

        return response()->json($kabupaten);
    }

    /**
     * Get statistics untuk dashboard pencarian
     */
    public function getStatistics()
    {
        $stats = [
            'total_customers' => Pelanggan::count(),
            'customers_with_fat' => Pelanggan::whereNotNull('kode_fat')->where('kode_fat', '!=', '')->count(),
            'customers_with_coordinates' => Pelanggan::whereNotNull('latitude')->whereNotNull('longitude')->count(),
            'clusters' => Pelanggan::select('cluster', DB::raw('count(*) as total'))->groupBy('cluster')->get(),
            'provinsi' => Pelanggan::select('provinsi', DB::raw('count(*) as total'))->groupBy('provinsi')->get(),
            'kabupaten' => Pelanggan::select('kabupaten', 'provinsi', DB::raw('count(*) as total'))->groupBy('kabupaten', 'provinsi')->get(),
        ];

        return response()->json($stats);
    }

    // public function destroy($id)
    // {
    //     try {
    //         $pelanggan = Pelanggan::findOrFail($id);
    //         $pelanggan->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Data pelanggan berhasil dihapus.'
    //         ]);
    //     } catch (\Exception $e) {
    //         \Log::error('Gagal menghapus pelanggan: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menghapus data pelanggan.'
    //         ], 500);
    //     }
    // }


    public function __construct()
    {
        $this->middleware('auth');
    }
}
