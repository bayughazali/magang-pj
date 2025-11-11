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
        // 🔹 USER MANAGEMENT - Total User
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
        // 🔹 GRAFIK LINE: Tren Pelanggan 12 Bulan
        // ========================================
        $trenData = Pelanggan::trenBulanan12Bulan();

        // Inisialisasi array kosong
        $bulanLabels = [];
        $pelangganTren = [];

        // Loop dan extract data dengan aman
        foreach ($trenData as $data) {
            $bulanLabels[] = $data['label'];
            $pelangganTren[] = (int) $data['jumlah']; // Cast ke integer untuk memastikan
        }

        // ========================================
        // 🔹 GRAFIK BAR: Pelanggan per Provinsi Bulan Ini
        // ========================================
        $clusterData = Pelanggan::select('provinsi', DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->whereNotNull('provinsi')
            ->where('provinsi', '!=', '')
            ->groupBy('provinsi')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // Jika tidak ada data bulan ini, ambil data keseluruhan
        if ($clusterData->isEmpty()) {
            $clusterData = Pelanggan::select('provinsi', DB::raw('COUNT(*) as total'))
                ->whereNotNull('provinsi')
                ->where('provinsi', '!=', '')
                ->groupBy('provinsi')
                ->orderByDesc('total')
                ->take(6)
                ->get();
        }

        // Extract labels dan values
        $clusterLabels = $clusterData->pluck('provinsi')->toArray();
        $clusterValues = $clusterData->pluck('total')->map(function($val) {
            return (int) $val; // Cast ke integer
        })->toArray();

        // ========================================
        // ✅ AKTIVITAS SALES PER USER - BULAN INI
        // ========================================
        $salesActivities = $this->getSalesActivitiesPerUser($currentYear, $currentMonth);

        // ========================================
        // 🔹 DEBUG LOG (opsional, bisa dihapus nanti)
        // ========================================
        Log::info('Dashboard Chart Data', [
            'bulan_labels_count' => count($bulanLabels),
            'pelanggan_tren_count' => count($pelangganTren),
            'bulan_labels' => $bulanLabels,
            'pelanggan_tren' => $pelangganTren,
            'cluster_labels' => $clusterLabels,
            'cluster_values' => $clusterValues,
            'sales_activities_count' => $salesActivities->count()
        ]);

        // ========================================
        // 🔹 Return ke View
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
            'clusterValues',
            'salesActivities' // ✅ Tambahan untuk aktivitas sales
        ));
    }

    /**
     * ✅ Get sales activities grouped by user for current month
     * Mengkoneksikan dengan data User yang role = 'user'
     */
    private function getSalesActivitiesPerUser($year, $month)
    {
        // ✅ Ambil semua user dengan role 'user' saja (bukan admin)
        $users = User::where('role', 'user')
            ->orderBy('name')
            ->get();

        // Format data untuk tampilan dengan warna dan progress
        $salesData = [];
        $colors = ['primary', 'danger', 'warning', 'info', 'success', 'secondary', 'dark'];

        foreach ($users as $index => $user) {
            // ✅ Hitung jumlah aktivitas user ini bulan ini
            // Cocokkan berdasarkan nama user dengan field 'sales' di report_activities
            $totalActivities = ReportActivity::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->where(function($query) use ($user) {
                    // Cari berdasarkan nama lengkap atau sebagian nama
                    $query->where('sales', 'LIKE', '%' . $user->name . '%')
                          ->orWhere('sales', $user->name);
                })
                ->count();

            // Ambil initial dari nama (huruf pertama)
            $initial = strtoupper(substr(trim($user->name), 0, 1));

            // Hitung progress bar (max 10 aktivitas = 100%)
            $progress = min(($totalActivities / 10) * 100, 100);

            // ✅ Tambahkan foto user jika ada
            $photoUrl = $user->photo
                ? asset('storage/photo/' . $user->photo)
                : null;

            $salesData[] = [
                'name' => $user->name,
                'email' => $user->email,
                'initial' => $initial,
                'total' => $totalActivities,
                'color' => $colors[$index % count($colors)],
                'progress' => round($progress, 0),
                'photo' => $photoUrl, // ✅ Foto profil user
                'user_id' => $user->id
            ];
        }

        // ✅ Filter hanya user yang punya aktivitas, atau tampilkan semua
        // Uncomment baris berikut jika hanya ingin tampilkan user yang ada aktivitasnya:
        // $salesData = array_filter($salesData, fn($item) => $item['total'] > 0);

        return collect($salesData);
    }

    /**
     * ✅ Get detailed activities for a specific sales person (untuk modal)
     */
    public function getSalesDetails(Request $request)
    {
        $salesName = $request->input('sales');

        if (empty($salesName)) {
            return response()->json([
                'success' => false,
                'message' => 'Nama sales tidak boleh kosong'
            ], 400);
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Ambil semua aktivitas sales yang dipilih bulan ini
        $activities = ReportActivity::where('sales', $salesName)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function($activity) {
                return [
                    'date' => Carbon::parse($activity->tanggal)->format('d F Y'),
                    'activity' => $activity->aktivitas ?? '-',
                    'location' => $activity->cluster ?? '-',
                    'status' => $activity->status ?? 'proses',
                    'hasil_kendala' => $activity->hasil_kendala ?? '-'
                ];
            });

        Log::info('Sales Details Request', [
            'sales' => $salesName,
            'activities_count' => $activities->count()
        ]);

        return response()->json([
            'success' => true,
            'sales' => $salesName,
            'activities' => $activities
        ]);
    }
}
