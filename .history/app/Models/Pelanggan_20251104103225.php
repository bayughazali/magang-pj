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
    protected $primaryKey = 'id_pelanggan';   // ✅ primary pakai id_pelanggan
    public $incrementing = false;             // ✅ non auto increment
    protected $keyType = 'string';            // ✅ tipe primary string

    // Field yang bisa diisi mass assignment
    protected $fillable = [
        'id_pelanggan',
        'nama_pelanggan',
        'bandwidth',
        'alamat',
        'provinsi',        // Field baru
        'kabupaten',       // Field baru
        'latitude',
        'longitude',
        'nomor_telepon',
        'cluster',
        'kode_fat',
    ];

    // Cast untuk tipe data yang tepat
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'id_pelanggan';
    }

    // ========================================
    // 🔹 SCOPE FILTERS
    // ========================================

    /**
     * Scope untuk filter berdasarkan provinsi
     */
    public function scopeByProvinsi($query, $provinsi)
    {
        return $query->where('provinsi', $provinsi);
    }

    /**
     * Scope untuk filter berdasarkan kabupaten
     */
    public function scopeByKabupaten($query, $kabupaten)
    {
        return $query->where('kabupaten', $kabupaten);
    }

    /**
     * Scope untuk filter berdasarkan cluster
     */
    public function scopeByCluster($query, $cluster)
    {
        return $query->where('cluster', $cluster);
    }

    /**
     * Scope untuk filter berdasarkan bandwidth
     */
    public function scopeByBandwidth($query, $bandwidth)
    {
        return $query->where('bandwidth', $bandwidth);
    }

    /**
     * Scope untuk filter berdasarkan bulan dan tahun
     */
    public function scopeByMonthYear($query, $month, $year)
    {
        return $query->whereYear('created_at', $year)
                     ->whereMonth('created_at', $month);
    }

    // ========================================
    // 🔹 ACCESSORS (Getters)
    // ========================================

    /**
     * Accessor untuk format alamat lengkap
     */
    public function getAlamatLengkapAttribute()
    {
        $parts = array_filter([
            $this->alamat,
            $this->kabupaten,
            $this->provinsi
        ]);

        return implode(', ', $parts);
    }

    /**
     * Accessor untuk koordinat dalam format string
     */
    public function getKoordinatAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return null;
    }

    /**
     * Method untuk mendapatkan URL Google Maps
     */
    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }

    /**
     * Accessor untuk format bandwidth yang lebih rapi
     */
    public function getBandwidthFormatAttribute()
    {
        return $this->bandwidth;
    }

    /**
     * Accessor untuk nama lengkap dengan ID
     */
    public function getNamaLengkapAttribute()
    {
        return "{$this->id_pelanggan} - {$this->nama_pelanggan}";
    }

    // ========================================
    // 🔹 STATIC METHODS untuk DASHBOARD
    // ========================================

    /**
     * Get pelanggan terbaru
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function terbaru($limit = 10)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Hitung total pelanggan per provinsi
     *
     * @return \Illuminate\Support\Collection
     */
    public static function totalPerProvinsi()
    {
        return self::select('provinsi', DB::raw('COUNT(*) as total'))
            ->whereNotNull('provinsi')
            ->where('provinsi', '!=', '')
            ->groupBy('provinsi')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Hitung total pelanggan per kabupaten dalam provinsi tertentu
     *
     * @param string $provinsi
     * @return \Illuminate\Support\Collection
     */
    public static function totalPerKabupaten($provinsi = null)
    {
        $query = self::select('kabupaten', DB::raw('COUNT(*) as total'))
            ->whereNotNull('kabupaten')
            ->where('kabupaten', '!=', '');

        if ($provinsi) {
            $query->where('provinsi', $provinsi);
        }

        return $query->groupBy('kabupaten')
                    ->orderByDesc('total')
                    ->get();
    }

    /**
     * Hitung total pelanggan per cluster
     *
     * @return \Illuminate\Support\Collection
     */
    public static function totalPerCluster()
    {
        return self::select('cluster', DB::raw('COUNT(*) as total'))
            ->whereNotNull('cluster')
            ->where('cluster', '!=', '')
            ->groupBy('cluster')
            ->orderBy('cluster')
            ->get();
    }

    /**
     * Hitung total pelanggan per bandwidth
     *
     * @return \Illuminate\Support\Collection
     */
    public static function totalPerBandwidth()
    {
        return self::select('bandwidth', DB::raw('COUNT(*) as total'))
            ->whereNotNull('bandwidth')
            ->where('bandwidth', '!=', '')
            ->groupBy('bandwidth')
            ->orderByRaw("CAST(SUBSTRING_INDEX(bandwidth, ' ', 1) AS UNSIGNED)")
            ->get();
    }

    /**
     * Hitung total pelanggan per bulan dalam tahun tertentu
     *
     * @param int|null $year
     * @return array
     */
    public static function totalPerBulan($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        return self::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();
    }

    /**
     * Hitung total pelanggan AKUMULATIF dalam periode 12 bulan terakhir
     * Data akan menunjukkan pertumbuhan total pelanggan dari waktu ke waktu
     *
     * @return \Illuminate\Support\Collection
     */
    public static function trenBulanan12Bulan()
    {
        $hasil = [];

        // Loop 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subMonths($i);
            $bulan = $tanggal->month;
            $tahun = $tanggal->year;

            // Hitung TOTAL pelanggan sampai akhir bulan ini (akumulatif)
            $totalSampaiSekarang = self::where('created_at', '<=', $tanggal->endOfMonth())
                ->count();

            $hasil[] = [
                'label' => $tanggal->translatedFormat('M'),  // Des, Jan, Feb, dst
                'jumlah' => $totalSampaiSekarang
            ];
        }

        return collect($hasil);
    }

    /**
     * Get statistik total pelanggan
     *
     * @return array
     */
    public static function statistikTotal()
    {
        return [
            'total' => self::count(),
            'bulan_ini' => self::whereYear('created_at', date('Y'))
                               ->whereMonth('created_at', date('m'))
                               ->count(),
            'tahun_ini' => self::whereYear('created_at', date('Y'))
                              ->count(),
            'per_provinsi' => self::totalPerProvinsi(),
            'per_cluster' => self::totalPerCluster(),
            'per_bandwidth' => self::totalPerBandwidth(),
        ];
    }

    /**
     * Get pertumbuhan pelanggan bulan ini vs bulan lalu
     *
     * @return array
     */
    public static function pertumbuhanBulanan()
    {
        $bulanIni = self::whereYear('created_at', date('Y'))
                       ->whereMonth('created_at', date('m'))
                       ->count();

        $bulanLalu = self::whereYear('created_at', now()->subMonth()->year)
                        ->whereMonth('created_at', now()->subMonth()->month)
                        ->count();

        $persentase = $bulanLalu > 0
            ? (($bulanIni - $bulanLalu) / $bulanLalu) * 100
            : 0;

        return [
            'bulan_ini' => $bulanIni,
            'bulan_lalu' => $bulanLalu,
            'selisih' => $bulanIni - $bulanLalu,
            'persentase' => round($persentase, 2),
            'status' => $persentase >= 0 ? 'naik' : 'turun'
        ];
    }

    /**
     * Cari pelanggan berdasarkan keyword (nama, nomor telepon, atau kode FAT)
     *
     * @param string $keyword
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function cari($keyword)
    {
        return self::where('nama_pelanggan', 'like', "%{$keyword}%")
            ->orWhere('nomor_telepon', 'like', "%{$keyword}%")
            ->orWhere('id_pelanggan', 'like', "%{$keyword}%")
            ->orWhere('kode_fat', 'like', "%{$keyword}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get pelanggan dalam radius tertentu dari koordinat
     *
     * @param float $lat
     * @param float $lng
     * @param float $radius (dalam km)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function dalamRadius($lat, $lng, $radius = 5)
    {
        // Menggunakan formula Haversine untuk menghitung jarak
        return self::selectRaw("
                *,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) + sin(radians(?)) *
                sin(radians(latitude)))) AS jarak
            ", [$lat, $lng, $lat])
            ->having('jarak', '<', $radius)
            ->orderBy('jarak', 'asc')
            ->get();
    }

    // ========================================
    // 🔹 RELATIONSHIPS (jika diperlukan di masa depan)
    // ========================================

    /**
     * Relationship dengan model User (jika ada sales person)
     * Uncomment jika sudah ada relasi
     */
    // public function salesPerson()
    // {
    //     return $this->belongsTo(User::class, 'sales_id');
    // }

    /**
     * Relationship dengan model Transaksi (jika ada)
     * Uncomment jika sudah ada relasi
     */
    // public function transaksi()
    // {
    //     return $this->hasMany(Transaksi::class, 'pelanggan_id', 'id_pelanggan');
    // }
}
