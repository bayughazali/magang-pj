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
        // 🔹 SALES REPORT - Hitung Jumlah Data
        // ========================================
        $totalReportBulanIni = ReportActivity::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $totalReportBulanLalu = ReportActivity::whereYear('created_at', $bulanLalu->year)
            ->whereMonth('created_at', $bulanLalu->month)
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
            'salesActivities'
        ));
    }

    /**
     * ✅ Get sales activities grouped by user for current month
     */
    private function getSalesActivitiesPerUser($year, $month)
    {
        $users = User::where('role', 'user')
            ->orderBy('name')
            ->get();

        $salesData = [];
        $colors = ['primary', 'danger', 'warning', 'info', 'success', 'secondary', 'dark'];

        foreach ($users as $index => $user) {
            // Hitung jumlah aktivitas user ini bulan ini
            $totalActivities = ReportActivity::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->where(function($query) use ($user) {
                    $query->where('sales', 'LIKE', '%' . $user->name . '%')
                          ->orWhere('sales', $user->name);
                })
                ->count();

            $initial = strtoupper(substr(trim($user->name), 0, 1));
            $progress = min(($totalActivities / 10) * 100, 100);

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
                'photo' => $photoUrl,
                'user_id' => $user->id
            ];
        }

        return collect($salesData);
    }

    /**
     * ✅ Get detailed activities for a specific sales person
     * Menggabungkan data dari Report Activity, Competitor, dan Operational
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

        // ✅ 1. Ambil data dari Report Activity
        $reportActivities = ReportActivity::where('sales', $salesName)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
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

        // ✅ 2. Ambil data Competitor yang dibuat user ini bulan ini
        $competitorActivities = Competitor::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
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

        // ✅ 3. Ambil data Pelanggan (Operational) yang dibuat bulan ini
        $operationalActivities = Pelanggan::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
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

        // ✅ 4. Gabungkan semua aktivitas dan urutkan berdasarkan tanggal terbaru
        $allActivities = $reportActivities
            ->concat($competitorActivities)
            ->concat($operationalActivities)
            ->sortByDesc('created_at')
            ->values();

        // ✅ 5. Ambil data user
        $user = User::where('name', $salesName)->first();
        $userData = null;

        if ($user) {
            $userData = [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $user->photo ? asset('storage/photo/' . $user->photo) : null
            ];
        }

        Log::info('Sales Details Request', [
            'sales' => $salesName,
            'report_activities' => $reportActivities->count(),
            'competitor_activities' => $competitorActivities->count(),
            'operational_activities' => $operationalActivities->count(),
            'total_activities' => $allActivities->count()
        ]);

        return response()->json([
            'success' => true,
            'sales' => $salesName,
            'user' => $userData,
            'activities' => $allActivities
        ]);
    }
}
