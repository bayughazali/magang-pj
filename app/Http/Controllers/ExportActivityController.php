<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportActivity; // ✅ ubah ke model yang benar
use App\Exports\ActivityExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportActivityController extends Controller
{
    /**
     * Menampilkan halaman utama report activity dengan filter tanggal.
     */
    public function index(Request $request)
    {
        $query = ReportActivity::query(); // ✅ gunakan model ReportActivity

        // Filter tanggal jika diisi
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $activities = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('export.activity', compact('activities'));
    }

    /**
     * Export data activity ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new \App\Exports\ActivityExport($startDate, $endDate), 'report_activity.xlsx');
    }

    /**
     * Export data activity ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = ReportActivity::query(); // ✅ gunakan model ReportActivity

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $activities = $query->get();

        $pdf = Pdf::loadView('export.activity_pdf', compact('activities'));
        return $pdf->download('Report_Activity.pdf');
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new \App\Exports\ActivityExport($startDate, $endDate), 'report_activity.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
