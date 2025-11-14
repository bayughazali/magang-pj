<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';
    protected $primaryKey = 'id_pelanggan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pelanggan',
        'sales_name',
        'user_id',
        'nama_pelanggan',
        'nomor_telepon',
        'bandwidth',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kode_fat',
        'alamat',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'kecepatan' => 'array',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ✅ METHOD UNTUK TREN BULANAN 12 BULAN (FIXED)
     * Menghitung jumlah pelanggan baru per bulan dalam 12 bulan terakhir
     */
    public static function trenBulanan12Bulan()
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $data = self::select(
            DB::raw('YEAR(created_at) as tahun'),
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('COUNT(*) as jumlah')
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('tahun', 'bulan')
        ->orderBy('tahun', 'asc')
        ->orderBy('bulan', 'asc')
        ->get();

        // Format hasil menjadi array dengan semua 12 bulan
        $result = [];
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        // Generate 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $tahun = $date->year;
            $bulan = $date->month;
            
            $found = $data->where('tahun', $tahun)
                         ->where('bulan', $bulan)
                         ->first();
            
            // ✅ FIX: Ubah key 'bulan' menjadi 'label'
            $result[] = [
                'label' => $months[$bulan] . ' ' . $tahun,  // ✅ Key 'label' untuk DashboardController
                'bulan' => $bulan,                          // Tetap simpan untuk reference
                'tahun' => $tahun,                          // Tetap simpan untuk reference
                'jumlah' => $found ? $found->jumlah : 0
            ];
        }

        return collect($result);
    }

    /**
     * ✅ METHOD UNTUK STATISTIK TAMBAHAN
     * Total pelanggan aktif
     */
    public static function totalAktif()
    {
        return self::count();
    }

    /**
     * ✅ METHOD UNTUK PELANGGAN BARU BULAN INI
     */
    public static function pelanganBaruBulanIni()
    {
        return self::whereMonth('created_at', Carbon::now()->month)
                   ->whereYear('created_at', Carbon::now()->year)
                   ->count();
    }

    /**
     * ✅ METHOD UNTUK PELANGGAN PER PROVINSI
     */
    public static function perProvinsi()
    {
        return self::select('provinsi', DB::raw('COUNT(*) as total'))
                   ->groupBy('provinsi')
                   ->orderBy('total', 'desc')
                   ->get();
    }

    /**
     * ✅ METHOD UNTUK PELANGGAN PER BANDWIDTH
     */
    public static function perBandwidth()
    {
        return self::select('bandwidth', DB::raw('COUNT(*) as total'))
                   ->groupBy('bandwidth')
                   ->orderBy('total', 'desc')
                   ->get();
    }
}