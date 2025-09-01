<?php

namespace App\Exports;

use App\Models\ReportActivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivityExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return ReportActivity::select(
            'sales',
            'aktivitas',
            'tanggal',
            'lokasi',
            'cluster',
            'evidence',
            'hasil_kendala',
            'status'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Sales',
            'Aktivitas',
            'Tanggal',
            'Lokasi',
            'Cluster',
            'Evidence',
            'Hasil Kendala',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Bold header
        ];
    }
}
