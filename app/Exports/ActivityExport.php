<?php

namespace App\Exports;

use App\Models\ReportActivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivityExport implements FromCollection, WithHeadings, WithStyles
{
    protected $startDate;
    protected $endDate;

    // ✅ Tambahan constructor
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = ReportActivity::select(
            'sales',
            'aktivitas',
            'tanggal',
            'lokasi',
            'cluster',
            'evidence',
            'hasil_kendala',
            'status'
        );

        // ✅ Tambahan filter tanggal
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        }

        return $query->get();
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
