<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportActivity;
use App\Models\Competitor;
use App\Models\Report;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
   public function activityView() {
    $activities = ReportActivity::paginate(10); // ✅ sudah bisa pakai links()
    return view('export.activity', compact('activities'));
}

    public function exportActivityPdf() {
        $activities = ReportActivity::all();
        $pdf = Pdf::loadView('export.activity-pdf', compact('activities'));
        return $pdf->download('activity_report.pdf');
    }

    public function competitorView() {
        $competitors = Competitor::all();
        return view('export.competitor', compact('competitors'));
    }

    public function exportCompetitorPdf() {
        $competitors = Competitor::all();
        $pdf = Pdf::loadView('export.competitor-pdf', compact('competitors'));
        return $pdf->download('competitor_report.pdf');
    }

    public function operationalView() {
        $reports = Report::all();
        return view('export.operational', compact('reports'));
    }

    public function exportOperationalPdf() {
        $reports = Report::all();
        $pdf = Pdf::loadView('export.operational-pdf', compact('reports'));
        return $pdf->download('operational_report.pdf');
    }

    public function exportActivityCsv() {
    $activities = \App\Models\ReportActivity::all();
    $filename = "activity_report.csv";

    $handle = fopen($filename, 'w+');
    fputcsv($handle, ["No", "Sales", "Aktivitas", "Tanggal", "Lokasi", "Cluster", "Hasil", "Status"]);

    foreach($activities as $index => $act) {
        fputcsv($handle, [
            $index+1,
            $act->sales,
            $act->aktivitas,
            $act->tanggal,
            $act->lokasi,
            $act->cluster,
            $act->hasil,
            $act->status
        ]);
    }

    fclose($handle);

    return response()->download($filename, $filename, [
        'Content-Type' => 'text/csv',
    ]);
}

}
