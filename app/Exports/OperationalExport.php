<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OperationalExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Pelanggan::select(
            'id_pelanggan',
            'nama_pelanggan',
            'bandwidth',
            'nomor_telepon',
            'provinsi',
            'kabupaten',
            'alamat',
            'cluster',
            'kode_fat',
            'latitude',
            'longitude'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID Pelanggan',
            'Nama Pelanggan',
            'Bandwidth',
            'Nomor Telepon',
            'Provinsi',
            'Kabupaten',
            'Alamat',
            'Cluster',
            'Kode FAT',
            'Latitude',
            'Longitude'
        ];
    }
}
