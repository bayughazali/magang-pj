<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use App\Models\ReportActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportActivityController extends Controller
{
    // Data wilayah: Provinsi -> Kabupaten -> Kecamatan
    private function getWilayahData()
    {
        return [
            'Bali' => [
                'Badung' => ['Kuta', 'Mengwi', 'Abiansemal', 'Petang', 'Kuta Selatan', 'Kuta Utara'],
                'Bangli' => ['Susut', 'Bangli', 'Tembuku', 'Kintamani'],
                'Buleleng' => ['Gerokgak', 'Seririt', 'Busungbiu', 'Banjar', 'Sukasada', 'Sawan', 'Kubutambahan', 'Tejakula', 'Buleleng'],
                'Denpasar' => ['Denpasar Barat', 'Denpasar Timur', 'Denpasar Selatan', 'Denpasar Utara'],
                'Gianyar' => ['Sukawati', 'Blahbatuh', 'Gianyar', 'Tampaksiring', 'Ubud', 'Tegallalang', 'Payangan'],
                'Jembrana' => ['Negara', 'Mendoyo', 'Pekutatan', 'Melaya', 'Jembrana'],
                'Karangasem' => ['Rendang', 'Sidemen', 'Manggis', 'Karangasem', 'Abang', 'Bebandem', 'Selat', 'Kubu'],
                'Klungkung' => ['Nusa Penida', 'Banjarangkan', 'Klungkung', 'Dawan'],
                'Tabanan' => ['Kediri', 'Tabanan', 'Marga', 'Baturiti', 'Kerambitan', 'Selemadeg', 'Selemadeg Timur', 'Selemadeg Barat', 'Pupuan', 'Penebel']
            ],
            'Nusa Tenggara Barat (NTB)' => [
                'Bima' => ['Monta', 'Bolo', 'Woha', 'Belo', 'Wawo', 'Sape', 'Donggo', 'Sanggar', 'Ambalawi', 'Langgudu', 'Lambu', 'Wera', 'Palibelo', 'Tambora', 'Soromandi', 'Parado', 'Lambitu', 'Madapangga'],
                'Dompu' => ['Dompu', 'Kempo', 'Hu\'u', 'Kilo', 'Woja', 'Pekat', 'Manggelewa'],
                'Lombok Barat' => ['Gerung', 'Kediri', 'Narmada', 'Lingsar', 'Gunungsari', 'Sekotong', 'Lembar', 'Batulayar', 'Kuripan', 'Labuapi'],
                'Lombok Tengah' => ['Praya', 'Jonggat', 'Batukliang', 'Kopang', 'Janapria', 'Pringgarata', 'Praya Barat', 'Praya Barat Daya', 'Praya Timur', 'Pujut', 'Batukliang Utara'],
                'Lombok Timur' => ['Keruak', 'Sakra', 'Terara', 'Sikur', 'Masbagik', 'Sukamulia', 'Selong', 'Pringgabaya', 'Aikmel', 'Wanasaba', 'Sembalun', 'Montong Gading', 'Pringgasela', 'Suralaga', 'Suwela', 'Labuhan Haji', 'Sambelia', 'Jerowaru'],
                'Lombok Utara' => ['Tanjung', 'Gangga', 'Kayangan', 'Bayan', 'Pemenang'],
                'Kota Mataram' => ['Ampenan', 'Mataram', 'Cakranegara', 'Sekarbela', 'Selaparang', 'Sandubaya'],
                'Sumbawa' => ['Sumbawa', 'Labuhan Badas', 'Moyo Utara', 'Moyo Hilir', 'Ropang', 'Lape', 'Lenangguar', 'Alas', 'Plampang', 'Alas Barat', 'Batu Lanteh', 'Lunyuk', 'Utan', 'Rhee', 'Orong Telu', 'Labangka', 'Labuhan Badas', 'Unter Iwes', 'Buer', 'Maronge', 'Empang', 'Tarano', 'Lopok'],
                'Sumbawa Barat' => ['Poto Tano', 'Brang Rea', 'Taliwang', 'Seteluk', 'Brang Ene', 'Jereweh', 'Maluk', 'Sekongkang']
            ],
            'Nusa Tenggara Timur (NTT)' => [
                'Alor' => ['Alor Barat Laut', 'Alor Barat Daya', 'Alor Selatan', 'Alor Timur', 'Alor Timur Laut', 'Pantar', 'Pantar Barat', 'Pantar Barat Laut', 'Pantar Tengah', 'Pantar Timur', 'Kabola', 'Pureman', 'Mataru', 'Pulau Pura', 'Teluk Mutiara', 'Lembur', 'Pantar Tengah Timur'],
                'Belu' => ['Tasifeto Barat', 'Tasifeto Timur', 'Kakuluk Mesak', 'Raihat', 'Lasiolat', 'Raimanuk', 'Lamaknen', 'Kobalima', 'Kobalima Timur', 'Malaka Timur', 'Malaka Tengah', 'Wewiku', 'Weliman'],
                'Ende' => ['Maurole', 'Nangapanda', 'Ende', 'Ende Selatan', 'Ende Timur', 'Ende Tengah', 'Ende Utara', 'Detusoko', 'Wolowaru', 'Wolojita', 'Wewaria', 'Lio Timur', 'Ndona', 'Ndona Timur', 'Kelimutu', 'Detukeli', 'Maukaro', 'Lepembusu Kelisoke'],
                'Flores Timur' => ['Wotan Ulumando', 'Ile Bura', 'Titehena', 'Demon Pagong', 'Lewolema', 'Solor Barat', 'Solor Timur', 'Adonara', 'Adonara Barat', 'Adonara Timur', 'Kelubagolit', 'Witihama', 'Ile Boleng', 'Tanjung Bunga', 'Wulanggitang', 'Larantuka', 'Ile Mandiri', 'Ile Bura', 'Demon Pagong'],
                'Kupang' => ['Semau', 'Kupang Barat', 'Kupang Timur', 'Sulamu', 'Kupang Tengah', 'Amarasi', 'Fatuleu', 'Takari', 'Amfoang Selatan', 'Amfoang Utara', 'Nekamese', 'Amarasi Barat', 'Amarasi Selatan', 'Amarasi Timur', 'Amabi Oefeto', 'Amabi Oefeto Timur', 'Amfoang Barat Daya', 'Amfoang Barat Laut', 'Taebenu', 'Semau Selatan'],
                'Kota Kupang' => ['Oebobo', 'Kelapa Lima', 'Maulafa', 'Kota Raja', 'Alak', 'Kota Lama'],
                'Lembata' => ['Nubatukan', 'Atadei', 'Ile Ape', 'Lebatukan', 'Nubatukan', 'Omesuri', 'Buyasuri', 'Ile Ape Timur'],
                'Manggarai' => ['Reok', 'Satar Mese', 'Cibal', 'Langke Rembong', 'Ruteng', 'Rahong', 'Lelak', 'Wae Rii', 'Satar Mese Barat', 'Cibal Barat'],
                'Manggarai Barat' => ['Komodo', 'Sano Nggoang', 'Macang Pacar', 'Boleng', 'Lembor', 'Lembor Selatan', 'Welak', 'Kuwus', 'Kuwus Barat', 'Ndoso'],
                'Manggarai Timur' => ['Borong', 'Elar', 'Poco Ranaka', 'Sambi Rampas', 'Kota Komba', 'Lamba Leda', 'Elar Selatan', 'Poco Ranaka Timur'],
                'Nagekeo' => ['Nangaroro', 'Boawae', 'Aesesa', 'Mauponggo', 'Keo Tengah', 'Wolowae', 'Aesesa Selatan'],
                'Ngada' => ['Riung', 'Bajawa', 'Golewa', 'Aimere', 'Soa', 'Wolomeze', 'Inerie', 'Riung Barat', 'Bajawa Utara', 'Golewa Selatan', 'Golewa Barat'],
                'Rote Ndao' => ['Rote Barat', 'Rote Timur', 'Pantai Baru', 'Lobalain', 'Rote Tengah', 'Rote Selatan', 'Rote Barat Daya', 'Rote Barat Laut', 'Landu Leko', 'Ndao Nuse'],
                'Sabu Raijua' => ['Sabu Barat', 'Sabu Timur', 'Sabu Tengah', 'Sabu Liae', 'Hawu Mehara', 'Raijua'],
                'Sikka' => ['Paga', 'Mego', 'Lela', 'Nita', 'Alok', 'Palue', 'Nelle', 'Talibura', 'Waigete', 'Kewapante', 'Bola', 'Magepanda', 'Alok Barat', 'Alok Timur', 'Kangae', 'Doreng', 'Hewokloang', 'Tana Wawo', 'Mapitara', 'Waiblama', 'Ile Bura'],
                'Sumba Barat' => ['Kota Waikabubak', 'Loli', 'Tana Righu', 'Wanokaka'],
                'Sumba Barat Daya' => ['Wewewa Barat', 'Wewewa Timur', 'Wewewa Tengah', 'Wewewa Selatan', 'Wewewa Utara', 'Kota Tambolaka'],
                'Sumba Tengah' => ['Katiku Tana', 'Umbu Ratu Nggay', 'Umbu Ratu Nggay Barat', 'Mamboro'],
                'Sumba Timur' => ['Haharu', 'Kambera', 'Kanatang', 'Kambata Mapambuhang', 'Lewa', 'Lewa Tidahu', 'Tabundung', 'Paberiwai', 'Pahunga Lodu', 'Umalulu', 'Pandawai', 'Matawai La Pawu', 'Wulla Waijelu', 'Kahaungu Eti', 'Rindi', 'Karera', 'Pinupahar', 'Ngadu Ngala', 'Kambata Mapambuhang', 'Nggaha Oriangu'],
                'Timor Tengah Selatan' => ['Amanuban Barat', 'Amanuban Selatan', 'Amanuban Tengah', 'Amanuban Timur', 'Amanatun Selatan', 'Amanatun Utara', 'Batu Putih', 'Boking', 'Fatumnasi', 'Kolbano', 'Kok Baun', 'Kualin', 'Kuanfatu', 'Kuatnana', 'Mollo Barat', 'Mollo Selatan', 'Mollo Tengah', 'Mollo Utara', 'Noebana', 'Noebeba', 'Nunkolo', 'Polen', 'Santian', 'Toianas', 'Tobu'],
                'Timor Tengah Utara' => ['Biboki Moenleu', 'Biboki Selatan', 'Biboki Utara', 'Biboki Anleu', 'Biboki Feotleu', 'Biboki Tanpah', 'Insana', 'Insana Barat', 'Insana Fafinesu', 'Insana Tengah', 'Insana Utara', 'Kota Kefamenanu', 'Miomafo Barat', 'Miomafo Timur', 'Musi', 'Mutis', 'Noemuti', 'Noemuti Timur', 'Bikomi Nilulat', 'Bikomi Selatan', 'Bikomi Tengah', 'Bikomi Utara', 'Naibenu']
            ]
        ];
    }

    public function create()
    {
        $wilayahData = $this->getWilayahData();
        return view('report.activity', compact('wilayahData'));
    }

    public function index()
    {
        $reports = ReportActivity::latest()->get();
        $wilayahData = $this->getWilayahData();
        return view('report.activity', compact('reports', 'wilayahData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales' => 'required|string|max:255',
            'aktivitas' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'hasil_kendala' => 'nullable|string',
            'status' => 'required|in:selesai,proses'
        ]);

        // Handle file upload
        if ($request->hasFile('evidence')) {
            try {
                $file = $request->file('evidence');
                if ($file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('evidence', $filename, 'public');
                    if (Storage::disk('public')->exists($path)) {
                        $validated['evidence'] = $path;
                    } else {
                        return redirect()->back()->with('error', 'Gagal menyimpan file evidence')->withInput();
                    }
                } else {
                    return redirect()->back()->with('error', 'File evidence tidak valid')->withInput();
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error saat upload: ' . $e->getMessage())->withInput();
            }
        }

        try {
            ReportActivity::create($validated);
            return redirect()->route('reports.activity')->with('success', 'Report berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sales' => 'required|string|max:255',
            'aktivitas' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'hasil_kendala' => 'nullable|string',
            'status' => 'required|in:selesai,proses'
        ]);

        $report = ReportActivity::findOrFail($id);

        // Handle file upload
        if ($request->hasFile('evidence')) {
            try {
                $file = $request->file('evidence');
                if ($file->isValid()) {
                    if ($report->evidence && Storage::disk('public')->exists($report->evidence)) {
                        Storage::disk('public')->delete($report->evidence);
                    }
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('evidence', $filename, 'public');
                    if (Storage::disk('public')->exists($path)) {
                        $validated['evidence'] = $path;
                    } else {
                        return redirect()->back()->with('error', 'Gagal menyimpan file evidence baru');
                    }
                } else {
                    return redirect()->back()->with('error', 'File evidence tidak valid');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error saat upload: ' . $e->getMessage());
            }
        }

        try {
            $report->update($validated);
            return redirect()->route('reports.activity')->with('success', 'Report berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $report = ReportActivity::findOrFail($id);
            if ($report->evidence) {
                Storage::disk('public')->delete($report->evidence);
            }
            $report->delete();
            return redirect()->route('reports.activity')->with('success', 'Report berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $reports = ReportActivity::orderBy('tanggal', 'desc')->get();
            $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
                ->loadView('report.activity-pdf', compact('reports'));
            return $pdf->download('laporan-aktivitas-sales-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    // Helper method untuk get kabupaten by provinsi (untuk AJAX)
    public function getKabupaten($provinsi)
    {
        $wilayahData = $this->getWilayahData();
        $kabupaten = isset($wilayahData[$provinsi]) ? array_keys($wilayahData[$provinsi]) : [];
        return response()->json($kabupaten);
    }

    // Helper method untuk get kecamatan by kabupaten (untuk AJAX)
    public function getKecamatan($provinsi, $kabupaten)
    {
        $wilayahData = $this->getWilayahData();
        $kecamatan = isset($wilayahData[$provinsi][$kabupaten]) ? $wilayahData[$provinsi][$kabupaten] : [];
        return response()->json($kecamatan);
    }
}
