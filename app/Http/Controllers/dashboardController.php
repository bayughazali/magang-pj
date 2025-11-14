<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\User;
use App\Models\ReportActivity;
use App\Models\ReportCompetitor;
use App\Models\Competitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Helper function untuk menghitung persentase perubahan bulan ke bulan
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * ✅ INDEX - Halaman Dashboard
     */
    public function index()
    {
        // ========================================
        // 🔹 Waktu Sekarang & Bulan Sebelumnya
        // ========================================
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $bulanLalu = Carbon::now()->subMonth();

        // 🔴 PERUBAHAN: Ambil user yang login
        $currentUser = auth()->user();
        $isAdmin = $currentUser->role === 'admin';

        // ========================================
        // 🔴 SALES REPORT - GABUNGAN Report Activity + Competitor
        // ========================================
        if ($isAdmin) {
            // Admin: Hitung semua report activity
            $reportActivityCount = ReportActivity::whereYear('tanggal', $currentYear)
                ->whereMonth('tanggal', $currentMonth)
                ->count();

            $reportActivityCountLalu = ReportActivity::whereYear('tanggal', $bulanLalu->year)
                ->whereMonth('tanggal', $bulanLalu->month)
                ->count();

            // Admin: Hitung semua competitor
            $competitorCount = 0;
            $competitorCountLalu = 0;
            
            if (Schema::hasTable('competitors')) {
                $competitorCount = Competitor::whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count();
                
                $competitorCountLalu = Competitor::whereMonth('created_at', $bulanLalu->month)
                    ->whereYear('created_at', $bulanLalu->year)
                    ->count();
            }

            // ✅ TOTAL = Report Activity + Competitor
            $totalReportBulanIni = $reportActivityCount + $competitorCount;
            $totalReportBulanLalu = $reportActivityCountLalu + $competitorCountLalu;

        } else {
            // User: Hitung report activity milik user
            $reportActivityCount = ReportActivity::whereYear('tanggal', $currentYear)
                ->whereMonth('tanggal', $currentMonth)
                ->where('sales', $currentUser->name)
                ->count();

            $reportActivityCountLalu = ReportActivity::whereYear('tanggal', $bulanLalu->year)
                ->whereMonth('tanggal', $bulanLalu->month)
                ->where('sales', $currentUser->name)
                ->count();

            // User: Hitung competitor milik user
            $competitorCount = 0;
            $competitorCountLalu = 0;
            
            if (Schema::hasTable('competitors')) {
                $salesColumn = Schema::hasColumn('competitors', 'sales_name') ? 'sales_name' : 
                              (Schema::hasColumn('competitors', 'sales') ? 'sales' : null);
                
                if ($salesColumn) {
                    $competitorCount = Competitor::whereMonth('created_at', $currentMonth)
                        ->whereYear('created_at', $currentYear)
                        ->where($salesColumn, $currentUser->name)
                        ->count();
                    
                    $competitorCountLalu = Competitor::whereMonth('created_at', $bulanLalu->month)
                        ->whereYear('created_at', $bulanLalu->year)
                        ->where($salesColumn, $currentUser->name)
                        ->count();
                }
            }

            // ✅ TOTAL = Report Activity + Competitor
            $totalReportBulanIni = $reportActivityCount + $competitorCount;
            $totalReportBulanLalu = $reportActivityCountLalu + $competitorCountLalu;
        }

        $persenSales = $this->calculatePercentageChange($totalReportBulanIni, $totalReportBulanLalu);

        // ✅ LOG untuk debugging
        Log::info('=== SALES REPORT CALCULATION ===', [
            'user' => $currentUser->name,
            'role' => $currentUser->role,
            'report_activity_count' => $reportActivityCount ?? 0,
            'competitor_count' => $competitorCount ?? 0,
            'total_sales_report' => $totalReportBulanIni,
            'month' => $currentMonth,
            'year' => $currentYear
        ]);

        // ========================================
        // 🔴 OPERATIONAL REPORT - Filter berdasarkan role
        // ========================================
        if ($isAdmin) {
            $totalPelangganBulanIni = Pelanggan::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();

            $totalPelangganBulanLalu = Pelanggan::whereYear('created_at', $bulanLalu->year)
                ->whereMonth('created_at', $bulanLalu->month)
                ->count();
        } else {
            // User biasa - filter berdasarkan sales atau user_id
            $hasSalesColumn = Schema::hasColumn('pelanggans', 'sales');
            
            if ($hasSalesColumn) {
                $totalPelangganBulanIni = Pelanggan::whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $currentMonth)
                    ->where('sales', $currentUser->name)
                    ->count();

                $totalPelangganBulanLalu = Pelanggan::whereYear('created_at', $bulanLalu->year)
                    ->whereMonth('created_at', $bulanLalu->month)
                    ->where('sales', $currentUser->name)
                    ->count();
            } elseif (Schema::hasColumn('pelanggans', 'user_id')) {
                $totalPelangganBulanIni = Pelanggan::whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $currentMonth)
                    ->where('user_id', $currentUser->id)
                    ->count();

                $totalPelangganBulanLalu = Pelanggan::whereYear('created_at', $bulanLalu->year)
                    ->whereMonth('created_at', $bulanLalu->month)
                    ->where('user_id', $currentUser->id)
                    ->count();
            } else {
                $totalPelangganBulanIni = 0;
                $totalPelangganBulanLalu = 0;
                
                Log::warning('Kolom sales atau user_id tidak ditemukan di tabel pelanggans', [
                    'user' => $currentUser->name,
                    'table' => 'pelanggans'
                ]);
            }
        }

        $persenPelanggan = $this->calculatePercentageChange($totalPelangganBulanIni, $totalPelangganBulanLalu);

        // ========================================
        // 🔹 USER MANAGEMENT - Total User (untuk admin)
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
        $clusterValues = $clusterData->pluck('total')->map(fn($v) => (int)$v)->toArray();

        // ========================================
        // ✅ AKTIVITAS SALES PER USER
        // ========================================
        $salesActivities = $this->getSalesActivitiesPerUser($currentYear, $currentMonth);

        // ========================================
        // ✅ GRAFIK: Korelasi Aktivitas Sales vs Hasil Penjualan
        // ========================================
        $salesCorrelationData = $this->getSalesCorrelationData($currentYear, $currentMonth);

        // ========================================
        // 🔹 RETURN VIEW
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
            'salesActivities',
            'salesCorrelationData'
        ));
    }

    /**
     * ✅ Get sales activities per user - Admin lihat semua, User lihat sendiri
     */
    private function getSalesActivitiesPerUser($year, $month)
    {
        $currentUser = auth()->user();
        
        // Admin lihat semua user, User biasa lihat sendiri
        if ($currentUser->role === 'admin') {
            $users = User::where('role', 'user')->orderBy('name')->get();
        } else {
            $users = User::where('id', $currentUser->id)->get();
        }

        $salesData = [];
        $colors = ['primary', 'danger', 'warning', 'info', 'success', 'secondary', 'dark'];

        foreach ($users as $index => $user) {
            // ========================================
            // ✅ REPORT ACTIVITY COUNT
            // ========================================
            $reportActivityCount = ReportActivity::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->where('sales', $user->name)
                ->count();

            // ========================================
            // 🔴 REPORT COMPETITOR COUNT
            // ========================================
            $competitorCount = 0;

            if (Schema::hasTable('competitors')) {
                // Cek kolom 'sales_name' atau 'sales'
                $salesColumn = Schema::hasColumn('competitors', 'sales_name') ? 'sales_name' : 
                              (Schema::hasColumn('competitors', 'sales') ? 'sales' : null);
                
                if ($salesColumn) {
                    $competitorCount = Competitor::whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->where($salesColumn, $user->name)
                        ->count();
                }
            }

            // ========================================
            // 🔴 REPORT OPERATIONAL (PELANGGAN) COUNT
            // ========================================
            $pelangganCount = 0;
            
            if (Schema::hasTable('pelanggans') && Schema::hasColumn('pelanggans', 'sales')) {
                $pelangganCount = Pelanggan::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('sales', $user->name)
                    ->count();
            } elseif (Schema::hasTable('pelanggans') && Schema::hasColumn('pelanggans', 'user_id')) {
                $pelangganCount = Pelanggan::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('user_id', $user->id)
                    ->count();
            }

            // ========================================
            // ✅ TOTAL AKTIVITAS
            // ========================================
            $totalActivities = $reportActivityCount + $competitorCount + $pelangganCount;

            $initial = strtoupper(substr(trim($user->name), 0, 1));
            $progress = $totalActivities > 0 ? min(($totalActivities / 10) * 100, 100) : 0;

            // ✅ Perbaikan path foto profil - sesuaikan dengan kolom di database
            $photoUrl = null;
            if ($user->profile_photo_path) {
                // Jika menggunakan kolom profile_photo_path (dari profile page)
                $photoUrl = Storage::url($user->profile_photo_path);
            } elseif ($user->photo) {
                // Fallback ke kolom photo (legacy)
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
        }

        return collect($salesData)->sortByDesc('total')->values();
    }

    /**
     * ✅ Get sales correlation data (for chart)
     */
    private function getSalesCorrelationData($year, $month)
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        $correlationData = [];

        foreach ($users as $user) {
            $salesCount = 0;
            $salesColumn = null;

            if (Schema::hasTable('competitors')) {
                $salesColumn = Schema::hasColumn('competitors', 'sales_name') ? 'sales_name' : 
                              (Schema::hasColumn('competitors', 'sales') ? 'sales' : null);
                
                if ($salesColumn) {
                    $salesCount = Competitor::whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->where($salesColumn, $user->name)
                        ->count();
                }
            }

            $totalRevenue = 0;
            if (Schema::hasTable('competitors') && Schema::hasColumn('competitors', 'harga') && $salesColumn) {
                $totalRevenue = Competitor::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where($salesColumn, $user->name)
                    ->sum('harga');
            }

            if ($salesCount > 0 || $totalRevenue > 0) {
                $correlationData[] = [
                    'name' => $user->name,
                    'sales' => $salesCount,
                    'revenue' => $totalRevenue
                ];
            }
        }

        return collect($correlationData)->sortByDesc('sales')->values()->toArray();
    }

    /**
     * ✅ Get detailed activities - User hanya bisa lihat data sendiri (kecuali admin)
     */
    public function getSalesDetails(Request $request)
    {
        try {
            $salesName = $request->input('sales');
            $currentUser = auth()->user();

            if (empty($salesName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama sales tidak boleh kosong'
                ], 400);
            }

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $user = User::where('name', $salesName)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // 🔴 Validasi akses
            if ($currentUser->role !== 'admin' && $user->id !== $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melihat data user ini'
                ], 403);
            }

            // ========================================
            // ✅ REPORT ACTIVITIES
            // ========================================
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

            // ========================================
            // 🔴 REPORT COMPETITOR
            // ========================================
            $competitorActivities = collect([]);
            
            if (Schema::hasTable('competitors')) {
                $salesColumn = Schema::hasColumn('competitors', 'sales_name') ? 'sales_name' : 
                              (Schema::hasColumn('competitors', 'sales') ? 'sales' : null);
                
                if ($salesColumn) {
                    $competitorActivities = Competitor::whereMonth('created_at', $currentMonth)
                        ->whereYear('created_at', $currentYear)
                        ->where($salesColumn, $salesName)
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($comp) {
                            return [
                                'type' => 'Report Competitor',
                                'date' => Carbon::parse($comp->created_at)->format('d F Y'),
                                'day' => Carbon::parse($comp->created_at)->locale('id')->isoFormat('dddd'),
                                'activity' => "Input data competitor: " . ($comp->competitor_name ?? $comp->nama_competitor ?? 'N/A'),
                                'location' => $comp->cluster ?? $comp->lokasi ?? '-',
                                'status' => 'selesai',
                                'hasil_kendala' => isset($comp->paket) && isset($comp->harga) 
                                    ? "Paket: {$comp->paket}, Harga: Rp " . number_format($comp->harga, 0, ',', '.') 
                                    : '-',
                                'evidence' => null,
                                'created_at' => $comp->created_at,
                            ];
                        });
                }
            }

            // ========================================
            // 🔴 REPORT OPERATIONAL (PELANGGAN)
            // ========================================
            $operationalActivities = collect([]);
            
            if (Schema::hasTable('pelanggans')) {
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
                                'activity' => "Input pelanggan baru: " . ($pelanggan->nama_pelanggan ?? 'N/A'),
                                'location' => ($pelanggan->kecamatan ?? '-') . ", " . ($pelanggan->kabupaten ?? '-'),
                                'status' => 'selesai',
                                'hasil_kendala' => "Kode FAT: " . ($pelanggan->kode_fat ?? '-') . ", Bandwidth: " . ($pelanggan->bandwidth ?? '-'),
                                'evidence' => null,
                                'created_at' => $pelanggan->created_at,
                            ];
                        });
                } elseif (Schema::hasColumn('pelanggans', 'user_id')) {
                    $operationalActivities = Pelanggan::whereMonth('created_at', $currentMonth)
                        ->whereYear('created_at', $currentYear)
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($pelanggan) {
                            return [
                                'type' => 'Report Operational',
                                'date' => Carbon::parse($pelanggan->created_at)->format('d F Y'),
                                'day' => Carbon::parse($pelanggan->created_at)->locale('id')->isoFormat('dddd'),
                                'activity' => "Input pelanggan baru: " . ($pelanggan->nama_pelanggan ?? 'N/A'),
                                'location' => ($pelanggan->kecamatan ?? '-') . ", " . ($pelanggan->kabupaten ?? '-'),
                                'status' => 'selesai',
                                'hasil_kendala' => "Kode FAT: " . ($pelanggan->kode_fat ?? '-') . ", Bandwidth: " . ($pelanggan->bandwidth ?? '-'),
                                'evidence' => null,
                                'created_at' => $pelanggan->created_at,
                            ];
                        });
                }
            }

            // ========================================
            // ✅ GABUNGKAN SEMUA AKTIVITAS
            // ========================================
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

            return response()->json([
                'success' => true,
                'sales' => $salesName,
                'user' => $userData,
                'activities' => $allActivities
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getSalesDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Get competitor details for specific user
     */
    public function getCompetitorDetails(Request $request)
    {
        try {
            $salesName = $request->input('sales');
            $currentUser = auth()->user();

            if (empty($salesName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama sales tidak boleh kosong'
                ], 400);
            }

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $user = User::where('name', $salesName)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // 🔴 Validasi akses
            if ($currentUser->role !== 'admin' && $user->id !== $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melihat data user ini'
                ], 403);
            }

            // ========================================
            // ✅ Query Competitor Data
            // ========================================
            $competitorActivities = collect([]);
            
            if (Schema::hasTable('competitors')) {
                // Cek apakah ada kolom 'sales_name' atau 'sales'
                $salesColumn = Schema::hasColumn('competitors', 'sales_name') ? 'sales_name' : 
                              (Schema::hasColumn('competitors', 'sales') ? 'sales' : null);
                
                if ($salesColumn) {
                    $competitorActivities = Competitor::whereMonth('created_at', $currentMonth)
                        ->whereYear('created_at', $currentYear)
                        ->where($salesColumn, $salesName)
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($comp) {
                            return [
                                'type' => 'Report Competitor',
                                'date' => Carbon::parse($comp->created_at)->format('d F Y'),
                                'day' => Carbon::parse($comp->created_at)->locale('id')->isoFormat('dddd'),
                                'competitor_name' => $comp->competitor_name ?? 'N/A',
                                'cluster' => $comp->cluster ?? '-',
                                'paket' => $comp->paket ?? '-',
                                'kecepatan' => $comp->kecepatan ?? '-',
                                'kuota' => $comp->kuota ?? '-',
                                'harga' => $comp->harga ?? 0,
                                'fitur_tambahan' => $comp->fitur_tambahan ?? '-',
                                'keterangan' => $comp->keterangan ?? '-',
                                'created_at' => $comp->created_at,
                            ];
                        });
                }
            }

            $userData = [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $user->photo ? asset('storage/photo/' . $user->photo) : null
            ];

            return response()->json([
                'success' => true,
                'sales' => $salesName,
                'user' => $userData,
                'competitors' => $competitorActivities
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getCompetitorDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}