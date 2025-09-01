<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Competitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OperationalReportController extends Controller
{
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
        // ✅ pakai paginate agar bisa akses total(), firstItem(), lastItem(), links()
        $pelanggans = Pelanggan::orderBy('created_at', 'desc')->paginate(10);

        // ambil region
        $regionData = $this->getRegionData();

        // ambil competitor untuk dropdown (optional)
        $competitors = Competitor::select('cluster', 'kecepatan')->distinct()->get();

        return view('report.operational.index', compact('pelanggans', 'regionData', 'competitors'));
    }

    // API untuk mendapatkan kabupaten berdasarkan provinsi
    public function getKabupaten(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');
            if (empty($provinsi)) {
                return response()->json([
                    'kabupaten' => [],
                    'error' => 'Provinsi parameter is required'
                ], 400);
            }

            $regionData = $this->getRegionData();
            if (isset($regionData[$provinsi])) {
                return response()->json([
                    'kabupaten' => array_keys($regionData[$provinsi]),
                    'success' => true
                ]);
            }

            return response()->json([
                'kabupaten' => [],
                'error' => 'Provinsi not found',
                'available_provinces' => array_keys($regionData)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'kabupaten' => [],
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // API untuk mendapatkan kode FAT
    public function getKodeFat(Request $request)
    {
        try {
            $provinsi = $request->input('provinsi');
            $kabupaten = $request->input('kabupaten');

            if (empty($provinsi) || empty($kabupaten)) {
                return response()->json([
                    'kode_fat' => '',
                    'error' => 'Provinsi and kabupaten parameters are required'
                ], 400);
            }

            $regionData = $this->getRegionData();
            if (isset($regionData[$provinsi][$kabupaten])) {
                $count = Pelanggan::where('provinsi', $provinsi)
                                  ->where('kabupaten', $kabupaten)
                                  ->count();
                $kodeFat = $regionData[$provinsi][$kabupaten] . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

                return response()->json([
                    'kode_fat' => $kodeFat,
                    'success' => true
                ]);
            }

            return response()->json([
                'kode_fat' => '',
                'error' => 'Provinsi/Kabupaten combination not found'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'kode_fat' => '',
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk mendapatkan kecepatan berdasarkan cluster
    public function getKecepatanByCluster(Request $request)
    {
        $cluster = $request->get('cluster');
        $kecepatan = Competitor::where('cluster', $cluster)
                              ->select('kecepatan')
                              ->distinct()
                              ->whereNotNull('kecepatan')
                              ->pluck('kecepatan');
        return response()->json($kecepatan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan'   => 'required|string|max:100|unique:pelanggans,id_pelanggan',
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

        if (empty($validated['kode_fat'])) {
            $regionData = $this->getRegionData();
            if (isset($regionData[$validated['provinsi']][$validated['kabupaten']])) {
                $count = Pelanggan::where('provinsi', $validated['provinsi'])
                                 ->where('kabupaten', $validated['kabupaten'])
                                 ->count();
                $validated['kode_fat'] = $regionData[$validated['provinsi']][$validated['kabupaten']] . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            }
        }

        Pelanggan::create($validated);
        return redirect()->route('report.operational.index')
            ->with('success', "Data pelanggan {$validated['nama_pelanggan']} berhasil disimpan dengan kode FAT: {$validated['kode_fat']}!");
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

        $pelangganData->update($validated);
        return redirect()->route('report.operational.index')
            ->with('success', "Data pelanggan berhasil diperbarui!");
    }

    public function destroy($pelanggan)
    {
        $pelangganData = Pelanggan::findOrFail($pelanggan);
        $nama = $pelangganData->nama_pelanggan;
        $pelangganData->delete();
        return back()->with('success', "Data pelanggan {$nama} berhasil dihapus!");
    }
}
