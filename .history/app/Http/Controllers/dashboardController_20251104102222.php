    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Pelanggan;
    use App\Models\User;
    use Carbon\Carbon;
    use App\Models\ReportActivity;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

    class DashboardController extends Controller
    {
        public function index()
        {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $bulanLalu = Carbon::now()->subMonth();

            // ========================================
            // 🔹 SALES REPORT - Hitung Jumlah Data
            // ========================================
            $totalReportBulanIni = ReportActivity::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();

            $totalReportBulanLalu = ReportActivity::whereYear('created_at', $bulanLalu->year)
                ->whereMonth('created_at', $bulanLalu->month)
                ->count();

            $persenSales = $totalReportBulanLalu > 0
                ? (($totalReportBulanIni - $totalReportBulanLalu) / $totalReportBulanLalu) * 100
                : 0;

            // ========================================
            // 🔹 OPERATIONAL REPORT - Pelanggan
            // ========================================
            $totalPelangganBulanIni = Pelanggan::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();

            $totalPelangganBulanLalu = Pelanggan::whereYear('created_at', $bulanLalu->year)
                ->whereMonth('created_at', $bulanLalu->month)
                ->count();

            $persenPelanggan = $totalPelangganBulanLalu > 0
                ? (($totalPelangganBulanIni - $totalPelangganBulanLalu) / $totalPelangganBulanLalu) * 100
                : 0;

            // ========================================
            // 🔹 USER MANAGEMENT - Total User (SEMUA)
            // ========================================
            $totalUsers = User::count();

            $totalUsersBulanIni = User::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();

            $totalUsersBulanLalu = User::whereYear('created_at', $bulanLalu->year)
                ->whereMonth('created_at', $bulanLalu->month)
                ->count();

            $persenUsers = $totalUsersBulanLalu > 0
                ? (($totalUsersBulanIni - $totalUsersBulanLalu) / $totalUsersBulanLalu) * 100
                : 0;


            // ========================================
            // 🔹 GRAFIK LINE: Tren Pelanggan Bulanan (12 bulan terakhir)
            // ========================================
            $trenData = Pelanggan::trenBulanan12Bulan();

            $pelangganTren = [];
            $bulanLabels = [];

            foreach ($trenData as $data) {
                $bulanLabels[] = $data['label'];
                $pelangganTren[] = $data['jumlah'];
            }

            // ========================================
            // 🔹 GRAFIK BAR: Perbandingan per Provinsi Bulan Ini
            // ========================================
            $clusterData = Pelanggan::select('provinsi', DB::raw('COUNT(*) as total'))
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->whereNotNull('provinsi')
                ->where('provinsi', '!=', '')
                ->groupBy('provinsi')
                ->orderByDesc('total')
                ->take(6)
                ->pluck('total', 'provinsi')
                ->toArray();

            if (empty($clusterData)) {
                $clusterData = Pelanggan::select('provinsi', DB::raw('COUNT(*) as total'))
                    ->whereNotNull('provinsi')
                    ->where('provinsi', '!=', '')
                    ->groupBy('provinsi')
                    ->orderByDesc('total')
                    ->take(6)
                    ->pluck('total', 'provinsi')
                    ->toArray();
            }

            $clusterLabels = array_keys($clusterData);
            $clusterValues = array_values($clusterData);

            // ========================================
            // 🔹 DEBUG: Log data untuk pengecekan
            // ========================================
            Log::info('Dashboard Data:', [
                'user_id' => auth()->id(),
                'total_pelanggan' => Pelanggan::count(),
                'pelanggan_bulan_ini' => $totalPelangganBulanIni,
                'tren_data' => $pelangganTren,
                'provinsi_data' => $clusterData,
            ]);

            // ========================================
            // 🔹 Kirim Data ke View
            // ========================================
            return view('dashboard', compact(
                'totalReportBulanIni',
                'persenSales',
                'totalPelangganBulanIni',
                'persenPelanggan',
                'totalUsers',
                'persenUsers',
                'bulanLabels',
                'pelangganTren',
                'clusterLabels',
                'clusterValues'
            ));
        }
    }

    // ## 📁 **Struktur Folder untuk Gambar Promo** (Opsional)

    // Buat folder ini di project Anda:
    // ```
    // public/
    //   └── images/
    //       └── promo/
    //           ├── promo1.jpg
    //           ├── promo2.jpg
    //           ├── promo3.jpg
    //           └── promo4.jpg
