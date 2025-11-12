<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\User;
use App\Models\ReportActivity;
use App\Models\Competitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    // Helper function untuk menghitung persentase perubahan bulan ke bulan
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $bulanLalu = Carbon::now()->subMonth();

        // ========================================
        // 🔹 DEBUG: Tampilkan semua data sales
        // ========================================
        $allReportActivities = ReportActivity::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->select('id', 'sales', 'aktivitas', 'tanggal')
            ->get();
        
        Log::info('=== DEBUG ALL REPORT ACTIVITIES ===');
        foreach ($allReportActivities as $report) {
            Log::info("Report ID: {$report->id}", [
                'sales' => $report->sales,
                'sales_length' => strlen($report->sales),
                'sales_hex' => bin2hex($report->sales),
                'aktivitas' => $report->aktivitas,
                'tanggal' => $report->tanggal
            ]);
        }
        
        // Cek semua user
        $allUsers = User::where('role', 'user')->get();
        Log::info('=== DEBUG ALL USERS ===');
        foreach ($allUsers as $user) {
            Log::info("User ID: {$user->id}", [
                'name' => $user->name,
                'name_length' => strlen($user->name),
                'name_hex' => bin2hex($user->name),
                'email' => $user->email
            ]);
        }

        // ========================================
        // 🔹 SALES REPORT - Hitung Jumlah Data
        // ========================================
        $totalReportBulanIni = ReportActivity::whereYear('tanggal', $currentYear)
            ->whereMonth('tanggal', $currentMonth)
            ->count();

        $totalReportBulanLalu = ReportActivity::whereYear('tanggal', $bulanLalu->year)
            ->whereMonth('tanggal', $bulanLalu->month)
            ->count();

        $persenSales = $this->calculatePercentageChange($totalReportBulanIni, $totalReportBulanLalu);

        // ========================================
        // 🔹 OPERATIONAL REPORT - Pelanggan
        // ========================================
        $totalPelangganBulanIni = Pelanggan::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $totalPelangganBulanLalu = Pelanggan::whereYear('created_at', $bulanLalu->year)
            ->whereMonth('created_at', $bulanLalu->month)
            ->count();

        $persenPelanggan = $this->calculatePercentageChange($totalPelangganBulanIni, $totalPelangganBulanLalu);

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

        $persenUsers = $this->calculatePercentageChange($totalUsersBulanIni, $totalUsersBulanLalu);

        // ========================================
        // 🔹 GRAFIK LINE: Tren Pelanggan 12 Bulan
        // ========================================
        $trenData = Pelanggan::trenBulanan12Bulan();

        $bulanLabels = [];
        $pelangganTren = [];

        foreach ($trenData as $data) {
            $bulanLabels[] = $data['label'];
            $pelangganTren[] = (int) $data['jumlah'];
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

        if ($clusterData->isEmpty()) {
            $clusterData = Pelanggan::select('provinsi', DB::raw('COUNT(*) as total'))
                ->whereNotNull('provinsi')
                ->where('provinsi', '!=', '')
                ->groupBy('provinsi')
                ->orderByDesc('total')
                ->take(6)
                ->get();
        }

        $clusterLabels = $clusterData->pluck('provinsi')->toArray();
        $clusterValues = $clusterData->pluck('total')->map(function($val) {
            return (int) $val;
        })->toArray();

        // ========================================
        // ✅ AKTIVITAS SALES PER USER - BULAN INI
        // ========================================
        $salesActivities = $this->getSalesActivitiesPerUser($currentYear, $currentMonth);

        // ========================================
        // 🔹 DEBUG LOG
        // ========================================
        Log::info('Dashboard Chart Data', [
            'bulan_labels_count' => count($bulanLabels),
            'pelanggan_tren_count' => count($pelangganTren),
            'sales_activities_count' => $salesActivities->count(),
            'total_report_bulan_ini' => $totalReportBulanIni
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
            'salesActivities'
        ));
    }

    /**
     * ✅ Get sales activities grouped by user for current month
     * 🔧 Query berdasarkan nama user yang login (field 'sales')
     */
    private function getSalesActivitiesPerUser($year, $month)
    {
        $users = User::where('role', 'user')
            ->orderBy('name')
            ->get();

        $salesData = [];
        $colors = ['primary', 'danger', 'warning', 'info', 'success', 'secondary', 'dark'];

        foreach ($users as $index => $user) {
            // ✅ Query Report Activity berdasarkan nama user
            $reportActivityCount = ReportActivity::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->where('sales', $user->name)
                ->count();

            // ✅ Query Competitor - cek apakah ada field 'sales'
            $competitorCount = 0;
            if (Schema::hasColumn('competitors', 'sales')) {
                $competitorCount = Competitor::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('sales', $user->name)
                    ->count();
            }

            // ✅ Query Pelanggan - cek apakah ada field 'sales'
            $pelangganCount = 0;
            if (Schema::hasColumn('pelanggans', 'sales')) {
                $pelangganCount = Pelanggan::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('sales', $user->name)
                    ->count();
            }

            $totalActivities = $reportActivityCount + $competitorCount + $pelangganCount;

            $initial = strtoupper(substr(trim($user->name), 0, 1));
            $progress = $totalActivities > 0 ? min(($totalActivities / 10) * 100, 100) : 0;

            $photoUrl = null;
            if ($user->photo) {
                $photoUrl = asset('storage/photo/' . $user->photo);
            }

            $salesData[] = [
                'name' => $user->name,
                'email' => $user->email,
                'initial' => $initial,
                'total' => $totalActivities,
                'report_activity' => $reportActivityCount,
                'competitor' => $competitorCount,
                'operational' => $pelangganCount,
                'color' => $colors[$index % count($colors)],
                'progress' => round($progress, 0),
                'photo' => $photoUrl,
                'user_id' => $user->id,
            ];

            // ✅ Log untuk debugging
            Log::info("Dashboard - User: {$user->name}", [
                'report_activity' => $reportActivityCount,
                'competitor' => $competitorCount,
                'pelanggan' => $pelangganCount,
                'total' => $totalActivities
            ]);
        }

        return collect($salesData)->sortByDesc('total')->values();
    }

    /**
     * ✅ Get detailed activities for a specific sales person
     * 🔧 Query berdasarkan nama user dari field 'sales'
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

        // ✅ Cari user berdasarkan nama
        $user = User::where('name', $salesName)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        Log::info('Sales Details - Request', [
            'sales_name' => $salesName,
            'month' => $currentMonth,
            'year' => $currentYear
        ]);

        // ✅ Query Report Activities
        $reportActivities = ReportActivity::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('sales', $salesName)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function($activity) {
                return [
                    'type' => 'Report Activity',
                    'date' => Carbon::parse($activity->tanggal)->format('d F Y'),
                    'day' => Carbon::parse($activity->tanggal)->locale('id')->isoFormat('dddd'),
                    'activity' => $activity->aktivitas ?? '-',
                    'location' => $activity->cluster ?? '-',
                    'status' => $activity->status ?? 'proses',
                    'hasil_kendala' => $activity->hasil_kendala ?? '-',
                    'evidence' => $activity->evidence ? asset('storage/' . $activity->evidence) : null,
                    'created_at' => $activity->created_at,
                ];
            });

        // ✅ Query Competitor jika ada field 'sales'
        $competitorActivities = collect([]);
        if (Schema::hasColumn('competitors', 'sales')) {
            $competitorActivities = Competitor::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('sales', $salesName)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($comp) {
                    return [
                        'type' => 'Report Competitor',
                        'date' => Carbon::parse($comp->created_at)->format('d F Y'),
                        'day' => Carbon::parse($comp->created_at)->locale('id')->isoFormat('dddd'),
                        'activity' => "Input data competitor: {$comp->competitor_name}",
                        'location' => $comp->cluster ?? '-',
                        'status' => 'selesai',
                        'hasil_kendala' => "Paket: {$comp->paket}, Harga: Rp " . number_format($comp->harga, 0, ',', '.'),
                        'evidence' => null,
                        'created_at' => $comp->created_at,
                    ];
                });
        }

        // ✅ Query Pelanggan jika ada field 'sales'
        $operationalActivities = collect([]);
        if (Schema::hasColumn('pelanggans', 'sales')) {
            $operationalActivities = Pelanggan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('sales', $salesName)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($pelanggan) {
                    return [
                        'type' => 'Report Operational',
                        'date' => Carbon::parse($pelanggan->created_at)->format('d F Y'),
                        'day' => Carbon::parse($pelanggan->created_at)->locale('id')->isoFormat('dddd'),
                        'activity' => "Input pelanggan baru: {$pelanggan->nama_pelanggan}",
                        'location' => "{$pelanggan->kecamatan}, {$pelanggan->kabupaten}",
                        'status' => 'selesai',
                        'hasil_kendala' => "Kode FAT: {$pelanggan->kode_fat}, Bandwidth: {$pelanggan->bandwidth}",
                        'evidence' => null,
                        'created_at' => $pelanggan->created_at,
                    ];
                });
        }

        $allActivities = $reportActivities
            ->concat($competitorActivities)
            ->concat($operationalActivities)
            ->sortByDesc('created_at')
            ->values();

        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $user->photo ? asset('storage/photo/' . $user->photo) : null
        ];

        Log::info('Sales Details - Found', [
            'report_activities' => $reportActivities->count(),
            'competitor_activities' => $competitorActivities->count(),
            'operational_activities' => $operationalActivities->count(),
            'total' => $allActivities->count()
        ]);

        return response()->json([
            'success' => true,
            'sales' => $salesName,
            'user' => $userData,
            'activities' => $allActivities
        ]);
    }
}