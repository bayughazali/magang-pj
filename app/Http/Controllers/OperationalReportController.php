<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Competitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OperationalReportController extends Controller
{
    private function generateNextCustomerId(): string
    {
        $today = Carbon::now('Asia/Makassar')->format('ymd');
        $prefix = 'IP' . $today;

        $lastIdToday = Pelanggan::where('id_pelanggan', 'like', $prefix.'%')
            ->orderBy('id_pelanggan', 'desc')
            ->value('id_pelanggan');

        $seq = 0;
        if ($lastIdToday) {
            $seq = (int)substr($lastIdToday, strlen($prefix));
        }

        $nextSeq = str_pad((string)($seq + 1), 2, '0', STR_PAD_LEFT);
        return $prefix . $nextSeq;
    }

    private function getRegionDataWithKecamatan()
    {
        return [
            'Bali' => [
                'Badung' => [
                    'kecamatan' => ['Kuta', 'Kuta Selatan', 'Kuta Utara', 'Mengwi', 'Abiansemal', 'Petang'],
                    'kode_fat' => 'FAT-BDG'
                ],
                'Bangli' => [
                    'kecamatan' => ['Bangli', 'Susut', 'Tembuku', 'Kintamani'],
                    'kode_fat' => 'FAT-BGL'
                ],
                'Buleleng' => [
                    'kecamatan' => ['Singaraja', 'Buleleng', 'Sukasada', 'Sawan', 'Kubutambahan', 'Tejakula', 'Seririt', 'Busungbiu', 'Banjar'],
                    'kode_fat' => 'FAT-BLL'
                ],
                'Denpasar' => [
                    'kecamatan' => ['Denpasar Barat', 'Denpasar Timur', 'Denpasar Selatan', 'Denpasar Utara'],
                    'kode_fat' => 'FAT-DPS'
                ],
                'Gianyar' => [
                    'kecamatan' => ['Gianyar', 'Blahbatuh', 'Sukawati', 'Ubud', 'Tegallalang', 'Tampaksiring', 'Payangan'],
                    'kode_fat' => 'FAT-GNY'
                ],
                'Jembrana' => [
                    'kecamatan' => ['Negara', 'Mendoyo', 'Pekutatan', 'Melaya', 'Jembrana'],
                    'kode_fat' => 'FAT-JMB'
                ],
                'Karangasem' => [
                    'kecamatan' => ['Karangasem', 'Abang', 'Bebandem', 'Rendang', 'Sidemen', 'Manggis', 'Selat', 'Kubu'],
                    'kode_fat' => 'FAT-KAS'
                ],
                'Klungkung' => [
                    'kecamatan' => ['Semarapura', 'Banjarangkan', 'Klungkung', 'Dawan'],
                    'kode_fat' => 'FAT-KLK'
                ],
                'Tabanan' => [
                    'kecamatan' => ['Tabanan', 'Kediri', 'Marga', 'Selemadeg', 'Kerambitan', 'Penebel'],
                    'kode_fat' => 'FAT-TBN'
                ]
            ],
            'Nusa Tenggara Barat' => [
                'Bima' => [
                    'kecamatan' => ['Bima', 'Palibelo', 'Donggo', 'Sanggar', 'Woha'],
                    'kode_fat' => 'FAT-BIM'
                ],
                'Dompu' => [
                    'kecamatan' => ['Dompu', 'Kempo', 'Hu\'u', 'Kilo', 'Woja'],
                    'kode_fat' => 'FAT-DOM'
                ],
                'Lombok Barat' => [
                    'kecamatan' => ['Gerung', 'Kediri', 'Narmada', 'Lingsar', 'Gunungsari', 'Labuapi', 'Lembar', 'Sekotong', 'Kuripan'],
                    'kode_fat' => 'FAT-LBR'
                ],
                'Lombok Tengah' => [
                    'kecamatan' => ['Praya', 'Pujut', 'Jonggat', 'Batukliang', 'Kopang', 'Janapria', 'Pringgarata', 'Praya Barat', 'Praya Timur'],
                    'kode_fat' => 'FAT-LTG'
                ],
                'Lombok Timur' => [
                    'kecamatan' => ['Selong', 'Masbagik', 'Aikmel', 'Pringgabaya', 'Labuhan Haji', 'Sakra', 'Terara', 'Montong Gading', 'Suwela'],
                    'kode_fat' => 'FAT-LTM'
                ],
                'Lombok Utara' => [
                    'kecamatan' => ['Tanjung', 'Gangga', 'Kayangan', 'Bayan', 'Pemenang'],
                    'kode_fat' => 'FAT-LUT'
                ],
                'Mataram' => [
                    'kecamatan' => ['Ampenan', 'Mataram', 'Cakranegara', 'Sekarbela', 'Sandubaya', 'Selaparang'],
                    'kode_fat' => 'FAT-MTR'
                ],
                'Sumbawa' => [
                    'kecamatan' => ['Sumbawa', 'Unter Iwes', 'Moyo Hilir', 'Moyo Hulu', 'Alas', 'Batu Lanteh'],
                    'kode_fat' => 'FAT-SBW'
                ],
                'Sumbawa Barat' => [
                    'kecamatan' => ['Taliwang', 'Jereweh', 'Sekongkang', 'Maluk', 'Brang Rea'],
                    'kode_fat' => 'FAT-SBR'
                ]
            ],
            'Nusa Tenggara Timur' => [
                'Alor' => [
                    'kecamatan' => ['Kalabahi', 'Alor Barat Daya', 'Alor Barat Laut', 'Alor Selatan', 'Alor Timur'],
                    'kode_fat' => 'FAT-ALR'
                ],
                'Belu' => [
                    'kecamatan' => ['Atambua', 'Tasifeto Barat', 'Tasifeto Timur', 'Malaka Barat', 'Malaka Tengah'],
                    'kode_fat' => 'FAT-BLU'
                ],
                'Ende' => [
                    'kecamatan' => ['Ende', 'Ndona', 'Nangapanda', 'Detusoko', 'Maurole', 'Wolowaru'],
                    'kode_fat' => 'FAT-END'
                ],
                'Flores Timur' => [
                    'kecamatan' => ['Larantuka', 'Ile Mandiri', 'Tanjung Bunga', 'Solor Timur', 'Solor Barat', 'Adonara Timur'],
                    'kode_fat' => 'FAT-FLT'
                ],
                'Kupang' => [
                    'kecamatan' => ['Kupang Tengah', 'Kupang Barat', 'Kupang Timur', 'Amarasi', 'Nekamese', 'Sulamu', 'Amfoang Selatan', 'Amfoang Utara'],
                    'kode_fat' => 'FAT-KPG'
                ]
            ]
        ];
    }

    private function getRegionData()
    {
        $fullData = $this->getRegionDataWithKecamatan();
        $result = [];
        
        foreach ($fullData as $provinsi => $kabupatenData) {
            foreach ($kabupatenData as $kabupaten => $data) {
                $result[$provinsi][$kabupaten] = $data['kode_fat'];
            }
        }
        
        return $result;
    }

    // ============================================================================
    // ✅ PERUBAHAN UTAMA: Filter data berdasarkan role user
    // ============================================================================
    public function index()
    {
        $user = Auth::user();
        
        // ✅ Jika admin, tampilkan semua data
        // ✅ Jika bukan admin, hanya tampilkan data milik user tersebut
        if ($user->role === 'admin') {
            $pelanggans = Pelanggan::orderBy('created_at', 'desc')->get();
        } else {
            $pelanggans = Pelanggan::where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();
        }
        
        $pakets = Competitor::select('paket')->distinct()->get();
        $regionData = $this->getRegionData();
        $nextId = $this->generateNextCustomerId();

        return view('report.operational.index', compact('pelanggans', 'pakets', 'regionData', 'nextId'));
    }

    public function getKabupaten(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');

            if (empty($provinsi)) {
                return response()->json([
                    'success' => false,
                    'kabupaten' => [],
                    'error' => 'Provinsi parameter is required'
                ], 400);
            }

            $regionData = $this->getRegionDataWithKecamatan();

            if (isset($regionData[$provinsi])) {
                $kabupaten = array_keys($regionData[$provinsi]);

                return response()->json([
                    'success' => true,
                    'kabupaten' => $kabupaten
                ]);
            }

            return response()->json([
                'success' => false,
                'kabupaten' => [],
                'error' => 'Provinsi not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Get Kabupaten Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'kabupaten' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getKecamatan(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');
            $kabupaten = $request->input('kabupaten');

            Log::info('Get Kecamatan Request:', [
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten
            ]);

            if (empty($provinsi) || empty($kabupaten)) {
                return response()->json([
                    'success' => false,
                    'kecamatan' => [],
                    'error' => 'Provinsi dan kabupaten harus dipilih'
                ], 400);
            }

            $regionData = $this->getRegionDataWithKecamatan();

            if (isset($regionData[$provinsi][$kabupaten]['kecamatan'])) {
                $kecamatanList = $regionData[$provinsi][$kabupaten]['kecamatan'];

                Log::info('Kecamatan Found:', [
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'kecamatan_count' => count($kecamatanList),
                    'kecamatan' => $kecamatanList
                ]);

                return response()->json([
                    'success' => true,
                    'kecamatan' => $kecamatanList
                ]);
            }

            return response()->json([
                'success' => false,
                'kecamatan' => [],
                'error' => 'Data kecamatan tidak ditemukan'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Get Kecamatan Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'kecamatan' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getKodeFat(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');
            $kabupaten = $request->input('kabupaten');
            $kecamatan = $request->input('kecamatan');

            Log::info('Get Kode FAT Request:', [
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'kecamatan' => $kecamatan
            ]);

            if (empty($provinsi) || empty($kabupaten)) {
                return response()->json([
                    'success' => false,
                    'kode_fat' => '',
                    'error' => 'Provinsi dan kabupaten harus dipilih'
                ], 400);
            }

            $regionData = $this->getRegionDataWithKecamatan();

            if (isset($regionData[$provinsi][$kabupaten])) {
                $baseFat = $regionData[$provinsi][$kabupaten]['kode_fat'];
                
                // Query untuk menghitung jumlah pelanggan
                $query = Pelanggan::where('provinsi', $provinsi)
                                 ->where('kabupaten', $kabupaten);
                
                if (!empty($kecamatan)) {
                    // Jika kecamatan dipilih, tambahkan ke filter
                    $query->where('kecamatan', $kecamatan);
                    $count = $query->count();
                    
                    // Ambil 3 karakter pertama dari kecamatan dan uppercase
                    $kecamatanCode = strtoupper(substr($kecamatan, 0, 3));
                    
                    // Format: FAT-XXX-YYY-001
                    $kodeFat = sprintf("%s-%s-%03d", $baseFat, $kecamatanCode, $count + 1);
                } else {
                    // Jika kecamatan tidak dipilih
                    $count = $query->count();
                    $kodeFat = sprintf("%s-%03d", $baseFat, $count + 1);
                }

                Log::info('Kode FAT Generated:', [
                    'kode_fat' => $kodeFat,
                    'count' => $count
                ]);

                return response()->json([
                    'success' => true,
                    'kode_fat' => $kodeFat
                ]);
            }

            return response()->json([
                'success' => false,
                'kode_fat' => '',
                'error' => 'Data provinsi/kabupaten tidak ditemukan'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Get Kode FAT Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'kode_fat' => '',
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'alamat'         => 'required|string',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'kecamatan'      => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'nomor_telepon'  => 'required|string|max:50',
            'kode_fat'       => 'nullable|string|max:100',
        ]);

        // ✅ PAKSA sales_name dan user_id dari user yang login (KEAMANAN)
        $validated['sales_name'] = Auth::user()->name;
        $validated['user_id'] = Auth::id();
        $validated['id_pelanggan'] = $this->generateNextCustomerId();
        $validated['kecepatan'] = [$validated['bandwidth']];

        // Generate kode FAT otomatis jika kosong
        if (empty($validated['kode_fat'])) {
            $regionData = $this->getRegionDataWithKecamatan();
            if (isset($regionData[$validated['provinsi']][$validated['kabupaten']])) {
                $baseFat = $regionData[$validated['provinsi']][$validated['kabupaten']]['kode_fat'];
                
                $query = Pelanggan::where('provinsi', $validated['provinsi'])
                                 ->where('kabupaten', $validated['kabupaten']);
                
                if (!empty($validated['kecamatan'])) {
                    $query->where('kecamatan', $validated['kecamatan']);
                    $count = $query->count();
                    $kecamatanCode = strtoupper(substr($validated['kecamatan'], 0, 3));
                    $validated['kode_fat'] = sprintf("%s-%s-%03d", $baseFat, $kecamatanCode, $count + 1);
                } else {
                    $count = $query->count();
                    $validated['kode_fat'] = sprintf("%s-%03d", $baseFat, $count + 1);
                }
            }
        }

        try {
            Pelanggan::create($validated);
            return redirect()->route('report.operational.index')
                ->with('success', "✅ Data pelanggan {$validated['nama_pelanggan']} berhasil disimpan! Kode FAT: {$validated['kode_fat']} | Sales: {$validated['sales_name']}");
        } catch (\Exception $e) {
            Log::error('Store Pelanggan Error:', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ============================================================================
    // ✅ PERUBAHAN: Edit hanya untuk data milik user sendiri (kecuali admin)
    // ============================================================================
    public function edit($id)
    {
        try {
            $user = Auth::user();
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();
            
            // ✅ Cek apakah user berhak mengedit data ini
            if ($user->role !== 'admin' && $pelanggan->user_id !== $user->id) {
                return redirect()
                    ->route('report.operational.index')
                    ->withErrors(['error' => '⛔ Anda tidak memiliki akses untuk mengedit data ini.']);
            }
            
            $regionData = $this->getRegionData();
            return view('report.operational.edit', compact('pelanggan', 'regionData'));

        } catch (\Exception $e) {
            Log::error("Gagal membuka form edit pelanggan ID {$id}: " . $e->getMessage());
            return redirect()
                ->route('report.operational.index')
                ->withErrors(['error' => 'Data pelanggan tidak ditemukan. ID: ' . $id]);
        }
    }

    // ============================================================================
    // ✅ PERUBAHAN: Update hanya untuk data milik user sendiri (kecuali admin)
    // ============================================================================
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_pelanggan'   => [
                'required',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('pelanggans', 'id_pelanggan')->ignore($id, 'id_pelanggan')
            ],
            'nama_pelanggan' => 'required|string|max:255',
            'bandwidth'      => 'required|string|max:100',
            'nomor_telepon'  => 'required|string|max:50',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'kecamatan'      => 'required|string|max:100',
            'kode_fat'       => 'nullable|string|max:100',
            'alamat'         => 'required|string',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $user = Auth::user();
            $pelanggan = Pelanggan::where('id_pelanggan', $id)->firstOrFail();
            
            // ✅ Cek apakah user berhak mengupdate data ini
            if ($user->role !== 'admin' && $pelanggan->user_id !== $user->id) {
                return redirect()
                    ->route('report.operational.index')
                    ->withErrors(['error' => '⛔ Anda tidak memiliki akses untuk mengubah data ini.']);
            }

            // ✅ HANYA ADMIN yang bisa ubah sales_name
            if ($user->role === 'admin' && $request->has('sales_name')) {
                $validated['sales_name'] = $request->sales_name;
            } else {
                // User biasa tidak bisa ubah sales_name
                $validated['sales_name'] = $pelanggan->sales_name;
                $validated['user_id'] = $pelanggan->user_id;
            }

            if ($validated['id_pelanggan'] !== $id) {
                $exists = Pelanggan::where('id_pelanggan', $validated['id_pelanggan'])->exists();
                if ($exists) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['id_pelanggan' => 'ID Pelanggan sudah digunakan']);
                }

                DB::transaction(function () use ($pelanggan, $validated) {
                    $oldId = $pelanggan->id_pelanggan;
                    $pelanggan->delete();
                    Pelanggan::create($validated);
                    Log::info("ID Pelanggan berhasil diubah dari {$oldId} ke {$validated['id_pelanggan']}");
                });
            } else {
                $pelanggan->update($validated);
            }

            return redirect()
                ->route('report.operational.index')
                ->with('success', "✅ Data pelanggan {$validated['nama_pelanggan']} berhasil diperbarui!");

        } catch (\Exception $e) {
            Log::error("Gagal mengupdate pelanggan ID {$id}: " . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ============================================================================
    // ✅ PERUBAHAN: Hapus hanya untuk data milik user sendiri (kecuali admin)
    // ============================================================================
    public function destroy($pelanggan)
    {
        try {
            $user = Auth::user();
            $pelangganData = Pelanggan::findOrFail($pelanggan);
            
            // ✅ Cek apakah user berhak menghapus data ini
            if ($user->role !== 'admin' && $pelangganData->user_id !== $user->id) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => '⛔ Anda tidak memiliki akses untuk menghapus data ini.'
                    ], 403);
                }
                return back()->withErrors(['error' => '⛔ Anda tidak memiliki akses untuk menghapus data ini.']);
            }
            
            $nama = $pelangganData->nama_pelanggan;
            $pelangganData->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "✅ Data pelanggan {$nama} berhasil dihapus!"
                ]);
            }
            
            return back()->with('success', "✅ Data pelanggan {$nama} berhasil dihapus!");
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}