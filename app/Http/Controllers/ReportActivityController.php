<?php

namespace App\Http\Controllers;

use App\Models\ReportActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportActivityController extends Controller
{
    /**
     * [DATA LENGKAP - DIPERBAIKI] Data lokasi untuk Provinsi Bali, NTB, dan NTT
     * Beserta seluruh kabupaten/kota dan kecamatannya.
     * (Kabupaten Tabanan sudah ditambahkan)
     */
    private function getLokasiData()
    {
        return [
            'Bali' => [
                'Kab. Badung' => ['Kuta', 'Kuta Selatan', 'Kuta Utara', 'Mengwi', 'Abiansemal', 'Petang'],
                'Kab. Bangli' => ['Bangli', 'Kintamani', 'Susut', 'Tembuku'],
                'Kab. Buleleng' => ['Buleleng', 'Banjar', 'Busungbiu', 'Gerokgak', 'Kubutambahan', 'Sawan', 'Seririt', 'Sukasada', 'Tejakula'],
                'Kab. Gianyar' => ['Gianyar', 'Blahbatuh', 'Payangan', 'Sukawati', 'Tampaksiring', 'Tegallalang', 'Ubud'],
                'Kab. Jembrana' => ['Jembrana', 'Melaya', 'Mendoyo', 'Negara', 'Pekutatan'],
                'Kab. Karangasem' => ['Karangasem', 'Abang', 'Bebandem', 'Kubu', 'Manggis', 'Rendang', 'Selat', 'Sidemen'],
                'Kab. Klungkung' => ['Klungkung', 'Banjarangkan', 'Dawan', 'Nusa Penida'],
                'Kab. Tabanan' => ['Tabanan', 'Kediri', 'Kerambitan', 'Selemadeg', 'Selemadeg Barat', 'Selemadeg Timur', 'Penebel', 'Pupuan', 'Marga', 'Baturiti'],
                'Kota Denpasar' => ['Denpasar Barat', 'Denpasar Selatan', 'Denpasar Timur', 'Denpasar Utara'],
            ],
            'Nusa Tenggara Barat (NTB)' => [
                'Kab. Bima' => ['Ambalawi', 'Belo', 'Bolo', 'Donggo', 'Lambitu', 'Lambu', 'Langgudu', 'Mada Pangga', 'Monta', 'Palibelo', 'Parado', 'Sanggar', 'Sape', 'Soromandi', 'Tambora', 'Wawo', 'Wera', 'Woha'],
                'Kab. Dompu' => ['Dompu', 'Hu\'u', 'Kilo', 'Kempo', 'Manggalewa', 'Pajo', 'Pekat', 'Woja'],
                'Kab. Lombok Barat' => ['Batu Layar', 'Gerung', 'Gunungsari', 'Kediri', 'Kuripan', 'Labuapi', 'Lembar', 'Narmada', 'Lingsar', 'Sekotong'],
                'Kab. Lombok Tengah' => ['Batukliang', 'Batukliang Utara', 'Janapria', 'Jonggat', 'Kopang', 'Praya', 'Praya Barat', 'Praya Barat Daya', 'Praya Tengah', 'Praya Timur', 'Pringgarata', 'Pujut'],
                'Kab. Lombok Timur' => ['Aikmel', 'Jerowaru', 'Keruak', 'Labuhan Haji', 'Masbagik', 'Montong Gading', 'Pringgabaya', 'Pringgasela', 'Sakra', 'Sakra Barat', 'Sakra Timur', 'Sambelia', 'Selong', 'Sembalun', 'Sikur', 'Sukamulia', 'Suralaga', 'Suwela', 'Terara', 'Wanasaba', 'Lenek'],
                'Kab. Lombok Utara' => ['Bayan', 'Gangga', 'Kayangan', 'Pemenang', 'Tanjung'],
                'Kab. Sumbawa' => ['Alas', 'Alas Barat', 'Badas', 'Batu Lanteh', 'Empang', 'Labangka', 'Labuhan Badas', 'Lantung', 'Lenangguar', 'Lope', 'Lunyuk', 'Maronge', 'Moyo Hilir', 'Moyo Hulu', 'Moyo Utara', 'Orong Telu', 'Plampang', 'Rhee', 'Ropang', 'Sumbawa', 'Tarano', 'Unter Iwes', 'Utan'],
                'Kab. Sumbawa Barat' => ['Brang Ene', 'Brang Rea', 'Jereweh', 'Maluk', 'Poto Tano', 'Seteluk', 'Taliwang', 'Sekongkang'],
                'Kota Bima' => ['Asakota', 'Mpunda', 'Raba', 'Rasanae Barat', 'Rasanae Timur'],
                'Kota Mataram' => ['Ampenan', 'Cakranegara', 'Mataram', 'Sandubaya', 'Sekarbela', 'Selaparang'],
            ],
            'Nusa Tenggara Timur (NTT)' => [
                'Kab. Alor' => ['Alor Barat Daya', 'Alor Barat Laut', 'Alor Selatan', 'Alor Tengah Utara', 'Alor Timur', 'Alor Timur Laut', 'Pulau Pura', 'Pantar', 'Pantar Barat', 'Pantar Timur', 'Pantar Tengah', 'Pantar Barat Laut', 'Mataru', 'Kabola', 'Teluk Mutiara', 'Lembur', 'Pureman', 'Abad Selatan'],
                'Kab. Belu' => ['Atambua', 'Atambua Barat', 'Atambua Selatan', 'Kakuluk Mesak', 'Lasiolat', 'Lamahak scarring Selatan', 'Raihat', 'Raimanuk', 'Tasifeto Barat', 'Tasifeto Timur', 'Nanaet Duabesi', 'Kobalima'],
                'Kab. Ende' => ['Ende', 'Ende Selatan', 'Ende Timur', 'Ende Tengah', 'Ende Utara', 'Nangapanda', 'Pulau Ende', 'Maukaro', 'Detusoko', 'Wewaria', 'Wolowaru', 'Wolojita', 'Kelimutu', 'Detukeli', 'Ndona', 'Ndona Timur', 'Lio Timur', 'Ndori', 'Kota Baru', 'Maurole', 'Lepembusu Kelisoke'],
                'Kab. Flores Timur' => ['Adonara', 'Adonara Barat', 'Adonara Tengah', 'Adonara Timur', 'Ile Boleng', 'Ile Mandiri', 'Larantuka', 'Lewolema', 'Solor Barat', 'Solor Timur', 'Tanjung Bunga', 'Wotan Ulumado', 'Wulanggitang', 'Demon Pagong', 'Ile Bura', 'Kelubagolit', 'Solor Selatan', 'Titehena', 'Witihama'],
                'Kab. Kupang' => ['Semau', 'Semau Selatan', 'Kupang Barat', 'Kupang Timur', 'Kupang Tengah', 'Nekamese', 'Taebenu', 'Amarasi', 'Amarasi Barat', 'Amarasi Selatan', 'Amarasi Timur', 'Fatuleu', 'Fatuleu Barat', 'Fatuleu Tengah', 'Takari', 'Amfoang Selatan', 'Amfoang Barat Daya', 'Amfoang Barat Laut', 'Amfoang Utara', 'Amfoang Timur', 'Amabi Oefeto Timur', 'Amabi Oefeto', 'Sulamu', 'Fatuleu'],
                'Kab. Lembata' => ['Atadei', 'Buyasari', 'Ile Ape', 'Ile Ape Timur', 'Lebatukan', 'Naga Wutung', 'Omesuri', 'Wulandoni', 'Nubatukan'],
                'Kab. Malaka' => ['Botin Leobele', 'Io Kufeu', 'Kobalima', 'Kobalima Timur', 'Laenmanen', 'Malaka Barat', 'Malaka Tengah', 'Rinhat', 'Sasita Mean', 'Wewiku', 'Weliman', 'Malaka Timur'],
                'Kab. Manggarai' => ['Cibal', 'Cibal Barat', 'Langke Rembong', 'Lelak', 'Rahong Utara', 'Reok', 'Reok Barat', 'Ruteng', 'Satar Mese', 'Satar Mese Barat', 'Satar Mese Utara', 'Wae Rii'],
                'Kab. Manggarai Barat' => ['Boleng', 'Komodo', 'Kuwus', 'Lembor', 'Lembor Selatan', 'Macang Pacar', 'Mbeliling', 'Ndoso', 'Sano Nggoang', 'Welak', 'Kuwus Barat', 'Pacar'],
                'Kab. Manggarai Timur' => ['Borong', 'Elar', 'Elar Selatan', 'Kota Komba', 'Lamba Leda', 'Poco Ranaka', 'Poco Ranaka Timur', 'Rana Mese', 'Sambi Rampas', 'Congkar', 'Lamba Leda Selatan', 'Lamba Leda Timur'],
                'Kab. Nagekeo' => ['Aesesa', 'Aesesa Selatan', 'Boawae', 'Mauponggo', 'Nangaroro', 'Keo Tengah', 'Wolowae'],
                'Kab. Ngada' => ['Aimere', 'Bajawa', 'Bajawa Utara', 'Golewa', 'Golewa Barat', 'Golewa Selatan', 'Inerie', 'Jerebuu', 'Riung', 'Riung Barat', 'Soa', 'Wolomeze'],
                'Kab. Rote Ndao' => ['Rote Barat', 'Rote Barat Daya', 'Rote Barat Laut', 'Rote Tengah', 'Rote Timur', 'Pantai Baru', 'Rote Selatan', 'Landu Leko', 'Ndao Nuse', 'Loaholu'],
                'Kab. Sabu Raijua' => ['Sabu Barat', 'Sabu Tengah', 'Sabu Timur', 'Sabu Liae', 'Hawu Mehara', 'Raijua'],
                'Kab. Sikka' => ['Alok', 'Alok Barat', 'Alok Timur', 'Bola', 'Doreng', 'Hewa Kliang', 'Kangae', 'Kewapante', 'Koting', 'Lela', 'Magepanda', 'Mapitara', 'Mego', 'Nelle', 'Nita', 'Paga', 'Palu\'e', 'Talibura', 'Tana Wawo', 'Waiblama', 'Waigete'],
                'Kab. Sumba Barat' => ['Loli', 'Kota Waikabubak', 'Wanokaka', 'Lamboya', 'Lamboya Barat', 'Tana Righu'],
                'Kab. Sumba Barat Daya' => ['Kodi', 'Kodi Bangedo', 'Kodi Balaghar', 'Kodi Utara', 'Wewewa Barat', 'Wewewa Selatan', 'Wewewa Timur', 'Wewewa Utara', 'Loura', 'Kota Tambolaka', 'Wewewa Tengah'],
                'Kab. Sumba Tengah' => ['Katikutana', 'Katikutana Selatan', 'Mamboro', 'Umbu Ratu Nggay', 'Umbu Ratu Nggay Barat'],
                'Kab. Sumba Timur' => ['Haharu', 'Kahaungu Eti', 'Kambata Mapambuhang', 'Kambera', 'Kanatang', 'Karera', 'Katala Hamu Lingu', 'Kota Waingapu', 'Lewa', 'Lewa Tidahu', 'Mahlon', 'Matawai La Pawu', 'Ngadu Ngala', 'Nggaha Oriangu', 'Paberiwai', 'Pahunga Lodu', 'Pandawai', 'Pinu Pahar', 'Rindi', 'Tabundung', 'Umalulu', 'Wula Waijelu'],
                'Kab. Timor Tengah Selatan' => ['Amanuban Barat', 'Amanuban Selatan', 'Amanuban Tengah', 'Amanuban Timur', 'Amanatun Selatan', 'Amanatun Utara', 'Batu Putih', 'Boking', 'Fatukopa', 'Fatumnasi', 'Fautmolo', 'Kie', 'Kok Baun', 'Kolbano', 'Kot\'olin', 'Kualin', 'Kuanfatu', 'Kuatnana', 'Mollo Barat', 'Mollo Selatan', 'Mollo Tengah', 'Mollo Utara', 'Noebana', 'Noebeba', 'Nunbena', 'Nunkolo', 'Oenino', 'Polen', 'Santian', 'Tobu', 'Toianas', 'Kota Soe'],
                'Kab. Timor Tengah Utara' => ['Biboki Anleu', 'Biboki Feotleu', 'Biboki Moenleu', 'Biboki Selatan', 'Biboki Tanpah', 'Biboki Utara', 'Insana', 'Insana Barat', 'Insana Fafinesu', 'Insana Tengah', 'Insana Utara', 'Kota Kefamenanu', 'Miomaffo Barat', 'Miomaffo Tengah', 'Miomaffo Timur', 'Musi', 'Mutis', 'Naibenu', 'Noemuti', 'Noemuti Timur', 'Bikomi Selatan', 'Bikomi Tengah', 'Bikomi Nilulat', 'Bikomi Utara'],
                'Kota Kupang' => ['Alak', 'Kelapa Lima', 'Kota Lama', 'Kota Raja', 'Maulafa', 'Oebobo'],
            ],
        ];
    }

    public function create()
    {
        $competitors = $this->getLokasiData();
        return view('report.activity', compact('competitors'));
    }

    /**
     * ✅ INDEX - Tampilkan data sesuai role
     * Admin: Lihat semua data
     * User: Lihat data sendiri saja
     */
    public function index()
    {
        $currentUser = Auth::user();
        
        // ✅ Filter berdasarkan role
        if ($currentUser->role === 'admin') {
            // Admin lihat semua data, urutkan terbaru
            $reports = ReportActivity::latest()->get();
        } else {
            // User biasa hanya lihat data sendiri
            $reports = ReportActivity::where('sales', $currentUser->name)
                ->latest()
                ->get();
        }
        
        $competitors = $this->getLokasiData();
        
        return view('report.activity', compact('reports', 'competitors'));
    }

    /**
     * ✅ STORE - Simpan data dengan nama sales otomatis
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales' => 'required|string|max:255',
            'aktivitas' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'cluster' => 'required|string|max:255',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'hasil_kendala' => 'nullable|string',
            'status' => 'required|in:selesai,proses'
        ]);

        // ✅ Validasi: Pastikan sales sesuai dengan user yang login (kecuali admin)
        if (Auth::user()->role !== 'admin') {
            if ($validated['sales'] !== Auth::user()->name) {
                return redirect()->back()
                    ->with('error', 'Anda tidak dapat membuat report atas nama orang lain!')
                    ->withInput();
            }
        }

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
                return redirect()->back()->with('error', 'Error saat upload: '. $e->getMessage())->withInput();
            }
        }

        try {
            ReportActivity::create($validated);
            return redirect()->route('reports.activity')->with('success', 'Report berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: '. $e->getMessage())->withInput();
        }
    }

    /**
     * ✅ UPDATE - User hanya bisa edit data sendiri, Admin bisa edit semua
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sales' => 'required|string|max:255',
            'aktivitas' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'cluster' => 'required|string|max:255',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'hasil_kendala' => 'nullable|string',
            'status' => 'required|in:selesai,proses'
        ]);

        $report = ReportActivity::findOrFail($id);

        // ✅ Validasi akses: User hanya bisa edit data sendiri
        if (Auth::user()->role !== 'admin') {
            if ($report->sales !== Auth::user()->name) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses untuk mengedit data ini!');
            }
            
            // User tidak boleh mengubah nama sales
            if ($validated['sales'] !== Auth::user()->name) {
                return redirect()->back()
                    ->with('error', 'Anda tidak dapat mengubah nama sales!')
                    ->withInput();
            }
        }

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
                return redirect()->back()->with('error', 'Error saat upload: '. $e->getMessage());
            }
        }

        try {
            $report->update($validated);
            return redirect()->route('reports.activity')->with('success', 'Report berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: '. $e->getMessage());
        }
    }

    /**
     * ✅ DESTROY - User hanya bisa hapus data sendiri, Admin bisa hapus semua
     */
    public function destroy($id)
    {
        try {
            $report = ReportActivity::findOrFail($id);
            
            // ✅ Validasi akses: User hanya bisa hapus data sendiri
            if (Auth::user()->role !== 'admin') {
                if ($report->sales !== Auth::user()->name) {
                    return redirect()->back()
                        ->with('error', 'Anda tidak memiliki akses untuk menghapus data ini!');
                }
            }
            
            if ($report->evidence) {
                Storage::disk('public')->delete($report->evidence);
            }
            $report->delete();
            return redirect()->route('reports.activity')->with('success', 'Report berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * ✅ EXPORT PDF - Admin lihat semua, User lihat sendiri
     */
    public function exportPdf()
    {
        try {
            $currentUser = Auth::user();
            
            // ✅ Filter berdasarkan role
            if ($currentUser->role === 'admin') {
                $reports = ReportActivity::orderBy('tanggal', 'desc')->get();
                $title = 'Laporan Aktivitas Sales - Semua User';
            } else {
                $reports = ReportActivity::where('sales', $currentUser->name)
                    ->orderBy('tanggal', 'desc')
                    ->get();
                $title = 'Laporan Aktivitas Sales - ' . $currentUser->name;
            }
            
            $data = [
                'reports' => $reports,
                'title'   => $title,
                'date'    => date('d F Y')
            ];
            
            $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
                ->loadView('report.activity-pdf', compact('reports', 'title'));
            
            return $pdf->download('laporan-aktivitas-sales-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    public function debugStorage()
    {
        $reports = ReportActivity::whereNotNull('evidence')->get();
        $debug = [];

        foreach ($reports as $report) {
            $debug[] = [
                'id' => $report->id,
                'evidence_path' => $report->evidence,
                'file_exists_public' => Storage::disk('public')->exists($report->evidence),
                'file_exists_storage' => file_exists(storage_path('app/public/' . $report->evidence)),
                'full_path' => storage_path('app/public/' . $report->evidence),
                'public_url' => asset('storage/' . $report->evidence),
                'storage_link_exists' => is_link(public_path('storage'))
            ];
        }

        $info = [
            'storage_path' => storage_path('app/public'),
            'public_path' => public_path('storage'),
            'storage_link_exists' => is_link(public_path('storage')),
            'storage_link_target' => is_link(public_path('storage')) ? readlink(public_path('storage')) : 'Not a symlink',
            'reports_with_evidence' => $reports->count(),
            'debug_data' => $debug
        ];

        return response()->json($info, 200, [], JSON_PRETTY_PRINT);
    }

    public function fixStorage()
    {
        try {
            if (is_link(public_path('storage'))) {
                unlink(public_path('storage'));
            }
            Artisan::call('storage:link');
            return redirect()->back()->with('success', 'Storage link berhasil diperbaiki!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbaiki storage link: ' . $e->getMessage());
        }
    }

    public function cleanupMissingFiles()
    {
        $reports = ReportActivity::whereNotNull('evidence')->get();
        $cleaned = 0;

        foreach ($reports as $report) {
            if (!Storage::disk('public')->exists($report->evidence)) {
                $report->update(['evidence' => null]);
                $cleaned++;
            }
        }

        return redirect()->back()->with('success', "Berhasil membersihkan {$cleaned} file yang hilang dari database.");
    }
}