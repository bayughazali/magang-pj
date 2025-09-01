<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\CompetitorExport;              // ✅ Import Export Class
use Maatwebsite\Excel\Facades\Excel;           // ✅ Import Excel Facade

class ExportCompetitorController extends Controller
{
    // Tampilan utama
    public function index()
    {
        $competitors = Competitor::all();
        return view('export.competitor', compact('competitors'));
    }

    // Export ke PDF
    public function exportPdf()
    {
        $competitors = Competitor::all();
        $pdf = Pdf::loadView('export.competitor_pdf', compact('competitors'));
        return $pdf->download('report-competitor.pdf');
    }

    // Export ke CSV
    public function exportCsv()
    {
        $competitors = Competitor::all();

        $response = new StreamedResponse(function () use ($competitors) {
            $handle = fopen('php://output', 'w');

            // Header kolom
            fputcsv($handle, [
                'No', 'Cluster', 'Nama Competitor', 'Paket', 'Kecepatan',
                'Kuota', 'Harga', 'Fitur Tambahan', 'Keterangan'
            ]);

            // Isi data
            foreach ($competitors as $index => $row) {
                fputcsv($handle, [
                    $index + 1,
                    $row->cluster,
                    $row->competitor_name,
                    $row->paket,
                    $row->kecepatan,
                    $row->kuota,
                    $row->harga,
                    $row->fitur_tambahan,
                    $row->keterangan,
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="report-competitor.csv"');

        return $response;
    }

    // Export ke Excel
    public function exportExcel()
    {
        return Excel::download(new CompetitorExport, 'competitor_report.xlsx');
    }
}
