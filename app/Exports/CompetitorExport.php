<?php

namespace App\Exports;

use App\Models\Competitor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompetitorExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Competitor::select(
            'cluster',
            'competitor_name',
            'paket',
            'kecepatan',
            'kuota',
            'harga',
            'fitur_tambahan',
            'keterangan'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Cluster',
            'Nama Competitor',
            'Paket',
            'Kecepatan',
            'Kuota',
            'Harga',
            'Fitur Tambahan',
            'Keterangan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header bold
        ];
    }
}
