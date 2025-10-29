<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\ReportActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\OperationalExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * ======== ACTIVITY REPORT VIEW ========
     */
    public function activityView()
    {
        $activities = ReportActivity::orderBy('tanggal', 'desc')->paginate(10);
        return view('export.activity', compact('activities'));
    }

    /**
     * ======== OPERATIONAL REPORT VIEW ========
     */
    public function operationalView()
    {
        $operationals = Pelanggan::orderBy('id', 'desc')->paginate(10);
        $operationalData = $operationals;

        return view('export.operational', compact('operationals', 'operationalData'));
    }

    /**
     * ======== DASHBOARD EXPORT PAGE ========
     */
    public function index()
    {
        $totalUsers = User::count();
        $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();

        return view('exports.index', compact('totalUsers', 'recentUsers'));
    }

    /**
     * ======== EXPORT ACTIVITY REPORT - PDF ========
     */
    public function exportActivityPdf()
    {
        $activities = ReportActivity::orderBy('tanggal', 'desc')->get();

        $pdf = Pdf::loadView('export.activity_pdf', compact('activities'))
                 ->setPaper('a4', 'landscape');

        return $pdf->download('Activity_Report_' . date('Ymd_His') . '.pdf');
    }

    /**
     * ======== EXPORT ACTIVITY REPORT - CSV ========
     */
    public function exportActivityCsv()
    {
        $fileName = 'Activity_Report_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $activities = ReportActivity::orderBy('tanggal', 'desc')->get();

        return response()->stream(function () use ($activities) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Sales', 'Aktivitas', 'Tanggal', 'Lokasi', 'Cluster', 'Hasil/Kendala', 'Status']);

            $no = 1;
            foreach ($activities as $a) {
                fputcsv($handle, [
                    $no++,
                    $a->sales,
                    $a->aktivitas,
                    $a->tanggal,
                    $a->lokasi,
                    $a->cluster,
                    $a->hasil_kendala,
                    $a->status,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * ======== EXPORT ACTIVITY REPORT - EXCEL ========
     */
    public function exportActivityExcel()
    {
        $activities = ReportActivity::orderBy('tanggal', 'desc')->get();
        $fileName = 'Activity_Report_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new \App\Exports\ActivityExport($activities), $fileName);
    }

    /**
     * ======== EXPORT OPERATIONAL REPORT - PDF ========
     */
    public function exportOperationalPdf()
    {
        $data = Pelanggan::all();

        $pdf = Pdf::loadView('export.operational_pdf', ['data' => $data])
                 ->setPaper('a4', 'landscape');

        return $pdf->download('Operational_Report_' . date('Ymd_His') . '.pdf');
    }

    /**
     * ======== EXPORT OPERATIONAL REPORT - CSV ========
     */
    public function exportOperationalCsv()
    {
        return Excel::download(new OperationalExport, 'Operational_Report_' . date('Ymd_His') . '.csv');
    }

    /**
     * ======== EXPORT OPERATIONAL REPORT - EXCEL ========
     */
    public function exportOperationalExcel()
    {
        return Excel::download(new OperationalExport, 'Operational_Report_' . date('Ymd_His') . '.xlsx');
    }
}
