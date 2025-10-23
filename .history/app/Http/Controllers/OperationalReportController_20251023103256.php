<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Competitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OperationalReportController extends Controller
{

    // ✳ : generator ID harian: IP + yymmdd + 2 digit urut per hari
    private function generateNextCustomerId(): string
    {
        $today = Carbon::now('Asia/Makassar')->format('ymd'); // 250904
        $prefix = 'IP' . $today;                              // IP250904

        // Ambil ID terakhir untuk hari ini lalu increment 2 digit di belakang
        $lastIdToday = Pelanggan::where('id_pelanggan', 'like', $prefix.'%')
            ->orderBy('id_pelanggan', 'desc')
            ->value('id_pelanggan');

        $seq = 0;
        if ($lastIdToday) {
            // Ambil 2 digit paling belakang sebagai urutan
            $seq = (int)substr($lastIdToday, strlen($prefix)); // contoh '01' -> 1
        }

        $nextSeq = str_pad((string)($seq + 1), 2, '0', STR_PAD_LEFT); // 01, 02, ...
        return $prefix . $nextSeq; // contoh: IP25090401
    }

    // Data provinsi dan kabupaten Indonesia Timur
    private function getRegionData()
    {
        return [
            'Bali' => [
                'Badung' => 'FAT-BDG',
                'Bangli' => 'FAT-BGL',
                'Buleleng' => 'FAT-BLL',
                'Denpasar' => 'FAT-DPS',
                'Gianyar' => 'FAT-GNY',
                'Jembrana' => 'FAT-JMB',
                'Karangasem' => 'FAT-KAS',
                'Klungkung' => 'FAT-KLK',
                'Tabanan' => 'FAT-TBN'
            ],
            'Nusa Tenggara Barat' => [
                'Bima' => 'FAT-BIM',
                'Dompu' => 'FAT-DOM',
                'Lombok Barat' => 'FAT-LBR',
                'Lombok Tengah' => 'FAT-LTG',
                'Lombok Timur' => 'FAT-LTM',
                'Lombok Utara' => 'FAT-LUT',
                'Mataram' => 'FAT-MTR',
                'Sumbawa' => 'FAT-SBW',
                'Sumbawa Barat' => 'FAT-SBR'
            ],
            'Nusa Tenggara Timur' => [
                'Alor' => 'FAT-ALR',
                'Belu' => 'FAT-BLU',
                'Ende' => 'FAT-END',
                'Flores Timur' => 'FAT-FLT',
                'Kupang' => 'FAT-KPG',
                'Lembata' => 'FAT-LMB',
                'Malaka' => 'FAT-MLK',
                'Manggarai' => 'FAT-MGG',
                'Manggarai Barat' => 'FAT-MGB',
                'Manggarai Timur' => 'FAT-MGT',
                'Nagekeo' => 'FAT-NGK',
                'Ngada' => 'FAT-NGD',
                'Rote Ndao' => 'FAT-RTN',
                'Sabu Raijua' => 'FAT-SBR',
                'Sikka' => 'FAT-SKK',
                'Sumba Barat' => 'FAT-SBT',
                'Sumba Barat Daya' => 'FAT-SBD',
                'Sumba Tengah' => 'FAT-STG',
                'Sumba Timur' => 'FAT-STM',
                'Timor Tengah Selatan' => 'FAT-TTS',
                'Timor Tengah Utara' => 'FAT-TTU'
            ]
        ];
    }

    public function index()
    {
    // Ambil semua data pelanggan untuk ditampilkan di tabel
    $pelanggans = Pelanggan::orderBy('created_at', 'desc')->get();

    // Ambil data paket & region (sesuai yang kamu pakai di blade)
    $pakets = Competitor::select('paket')->distinct()->get();
    $regionData = $this->getRegionData();

        // $lastId = Pelanggan::max('id_pelanggan');              // ❌ hapus cara lama (angka)
        // $nextId = $lastId ? $lastId + 1 : 1;                   // ❌ hapus

        $nextId = $this->generateNextCustomerId();               // ✳ ADD: preview ID format baru

        return view('report.operational.index', compact('pelanggans', 'pakets', 'regionData', 'nextId'));
    }

    // API untuk mendapatkan kabupaten berdasarkan provinsi - DENGAN DEBUGGING
    // ⬇ Tambahkan method create() di sini
    public function create()
    {
    $nextId = $this->generateNextCustomerId(); // ✅ langsung panggil fungsi generator

    // Ambil data tambahan untuk form (jika dibutuhkan)
    $regionData = $this->getRegionData();
    $pakets = Competitor::select('paket')->distinct()->pluck('paket');

    // arahkan ke view form create
    return view('report.operational.create', compact('nextId', 'regionData', 'pakets'));
}
    public function getKabupaten(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');

            // Log untuk debugging
            Log::info('Get Kabupaten Request:', [
                'provinsi' => $provinsi,
                'all_params' => $request->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

            // Validasi input
            if (empty($provinsi)) {
                Log::warning('Empty provinsi parameter');
                return response()->json([
                    'kabupaten' => [],
                    'error' => 'Provinsi parameter is required'
                ], 400);
            }

            $regionData = $this->getRegionData();

            // Log data region untuk debugging
            Log::info('Region Data Available:', [
                'provinces' => array_keys($regionData)
            ]);

            if (isset($regionData[$provinsi])) {
                $kabupaten = array_keys($regionData[$provinsi]);

                Log::info('Kabupaten Found:', [
                    'provinsi' => $provinsi,
                    'kabupaten_count' => count($kabupaten),
                    'kabupaten' => $kabupaten
                ]);

                return response()->json([
                    'kabupaten' => $kabupaten,
                    'success' => true
                ]);
            }

            Log::warning('Provinsi not found:', [
                'requested_provinsi' => $provinsi,
                'available_provinces' => array_keys($regionData)
            ]);

            return response()->json([
                'kabupaten' => [],
                'error' => 'Provinsi not found',
                'available_provinces' => array_keys($regionData)
            ]);

        } catch (\Exception $e) {
            Log::error('Get Kabupaten Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'kabupaten' => [],
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // API untuk mendapatkan kode FAT berdasarkan provinsi dan kabupaten - DENGAN DEBUGGING
    public function getKodeFat(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');
            $kabupaten = $request->input('kabupaten');

            // Log untuk debugging
            Log::info('Get Kode FAT Request:', [
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'all_params' => $request->all()
            ]);

            // Validasi input
            if (empty($provinsi) || empty($kabupaten)) {
                return response()->json([
                    'kode_fat' => '',
                    'error' => 'Provinsi and kabupaten parameters are required'
                ], 400);
            }

            $regionData = $this->getRegionData();

            if (isset($regionData[$provinsi][$kabupaten])) {
                // Ambil jumlah pelanggan yang sudah ada di kabupaten ini untuk numbering
                $count = Pelanggan::where('provinsi', $provinsi)
                                 ->where('kabupaten', $kabupaten)
                                 ->count();

                $kodeFat = $regionData[$provinsi][$kabupaten] . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

                Log::info('Kode FAT Generated:', [
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'existing_count' => $count,
                    'kode_fat' => $kodeFat
                ]);

                return response()->json([
                    'kode_fat' => $kodeFat,
                    'success' => true
                ]);
            }

            Log::warning('Provinsi/Kabupaten combination not found:', [
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten
            ]);

            return response()->json([
                'kode_fat' => '',
                'error' => 'Provinsi/Kabupaten combination not found'
            ]);

        } catch (\Exception $e) {
            Log::error('Get Kode FAT Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'kode_fat' => '',
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
        // Ambil data pelanggan agar bisa ditampilkan di tabel
        $pelanggans = Pelanggan::orderBy('created_at', 'desc')->paginate(10);

        // Ambil data competitor untuk dropdown cluster dan kecepatan
        $competitors = Competitor::select('cluster', 'kecepatan')->distinct()->get();

        return view('report.operational.index', compact('pelanggans', 'competitors'));
    }

    // Method untuk mendapatkan kecepatan berdasarkan cluster via AJAX
    public function getKecepatanByBandwidth(Request $request)
    {
        $kecepatan = $request->get('kecepatan');

        $paket = Competitor::where('kecepatan', $kecepatan)
                        ->select('speed')
                        ->distinct()
                        ->pluck('speed');

        return response()->json($paket);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'alamat'         => 'required|string',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'nomor_telepon'  => 'required|string|max:50',
            'cluster'        => 'required|string|max:100',
            'kode_fat'       => 'nullable|string|max:100',
        ]);

        $validated['id_pelanggan'] = $this->generateNextCustomerId();

        // Otomatis isi kecepatan sama dengan bandwidth pelanggan
        $validated['kecepatan'] = [$validated['bandwidth']];

        // Generate kode FAT otomatis jika kosong
        if (empty($validated['kode_fat'])) {
            $regionData = $this->getRegionData();
            if (isset($regionData[$validated['provinsi']][$validated['kabupaten']])) {
                $count = Pelanggan::where('provinsi', $validated['provinsi'])
                                 ->where('kabupaten', $validated['kabupaten'])
                                 ->count();

                $validated['kode_fat'] = $regionData[$validated['provinsi']][$validated['kabupaten']] . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            }
        }

        try {
            Pelanggan::create($validated);
            return redirect()->route('report.operational.index')
                ->with('success', "Data pelanggan {$validated['nama_pelanggan']} berhasil disimpan dengan kode FAT: {$validated['kode_fat']}!");
        } catch (\Exception $e) {
            Log::error('Store Pelanggan Error:', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

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

    public function update(Request $request, $pelanggan)
    {
        $pelangganData = Pelanggan::findOrFail($pelanggan);

        $validated = $request->validate([
            'id_pelanggan'   => 'required|string|max:100|unique:pelanggans,id_pelanggan,' . $pelangganData->id,
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'alamat'         => 'required|string',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'nomor_telepon'  => 'required|string|max:50',
            'cluster'        => 'required|string|max:100',
            'kode_fat'       => 'nullable|string|max:100',
        ]);

        try {
            $pelangganData->update($validated);
            return redirect()->route('report.operational.index')
                ->with('success', "Data pelanggan berhasil diperbarui!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($pelanggan)
    {
        try {
            $pelangganData = Pelanggan::findOrFail($pelanggan);
            $nama = $pelangganData->nama_pelanggan;
            $pelangganData->delete();
            return back()->with('success', "Data pelanggan {$nama} berhasil dihapus!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
