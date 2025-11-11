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

        $totalReportBulanIni = ReportActivity::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalReportBulanLalu = ReportActivity::whereYear('created_at', $bulanLalu->year)
            ->whereMonth('created_at', $bulanLalu->month)
            ->count();
        $persenSales = $this->calculatePercentageChange($totalReportBulanIni, $totalReportBulanLalu);

        $totalPelangganBulanIni = Pelanggan::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalPelangganBulanLalu = Pelanggan::whereYear('created_at', $bulanLalu->year)
            ->whereMonth('created_at', $bulanLalu->month)
            ->count();
        $persenPelanggan = $this->calculatePercentageChange($totalPelangganBulanIni, $totalPelangganBulanLalu);

        $totalUsers = User::count();
        $totalUsersBulanIni = User::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalUsersBulanLalu = User::whereYear('created_at', $bulanLalu->year)
            ->whereMonth('created_at', $bulanLalu->month)
            ->count();
        $persenUsers = $this->calculatePercentageChange($totalUsersBulanIni, $totalUsersBulanLalu);

        $salesActivities = $this->getSalesActivitiesPerUser($currentYear, $currentMonth);

        return view('dashboard', compact(
            'totalReportBulanIni',
            'persenSales',
            'totalPelangganBulanIni',
            'persenPelanggan',
            'totalUsers',
            'persenUsers',
            'salesActivities'
        ));
    }

    private function getSalesActivitiesPerUser($year, $month)
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        $salesData = [];
        $colors = ['primary', 'danger', 'warning', 'info', 'success', 'secondary', 'dark'];

        foreach ($users as $index => $user) {
            $totalActivities = ReportActivity::whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->where(function ($query) use ($user) {
                    $query->where('sales', 'LIKE', '%' . $user->name . '%')
                          ->orWhere('sales', $user->name);
                })
                ->count();

            $initial = strtoupper(substr(trim($user->name), 0, 1));
            $progress = min(($totalActivities / 10) * 100, 100);

            $photoUrl = $user->photo ? asset('storage/photo/' . $user->photo) : null;

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

    public function getSalesDetails(Request $request)
    {
        $salesName = $request->input('sales');
        if (empty($salesName)) {
            return response()->json(['success' => false, 'message' => 'Nama sales tidak boleh kosong'], 400);
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $reportActivities = ReportActivity::where('sales', $salesName)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->get();

        $competitorActivities = Competitor::where('sales', $salesName)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get();

        $operationalActivities = Pelanggan::where('sales', $salesName)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get();

        return response()->json([
            'success' => true,
            'sales' => $salesName,
            'report_activity' => $reportActivities->count(),
            'report_competitor' => $competitorActivities->count(),
            'report_operational' => $operationalActivities->count(),
        ]);
    }
}
