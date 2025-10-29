<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pelanggan; // tambahkan ini


class ReportOperational extends Model
{
    use HasFactory;

    protected $table = 'report_operational'; // Pastikan sesuai nama tabel kamu
    protected $fillable = [
        'id_pelanggan',
        'nama_pelanggan',
        'nomor_telepon',
        'provinsi',
        'kabupaten',
        'alamat',
        'cluster',
        'bandwidth',
        'kode_fat',
        'latitude',
        'longitude',
    ];
}
