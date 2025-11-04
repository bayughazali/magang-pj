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
        // 🔹 DEBUG LOG (opsional, bisa dihapus nanti)
        // ========================================
        Log::info('Dashboard Chart Data', [
            'bulan_labels_count' => count($bulanLabels),
            'pelanggan_tren_count' => count($pelangganTren),
            'bulan_labels' => $bulanLabels,
            'pelanggan_tren' => $pelangganTren,
            'cluster_labels' => $clusterLabels,
            'cluster_values' => $clusterValues,
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
            'clusterValues'
        ));
    }
}
