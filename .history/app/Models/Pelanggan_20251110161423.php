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
        'provinsi',
        'kabupaten',
        'kecamatan',        // ✅ Sudah pakai kecamatan, bukan cluster
        'latitude',
        'longitude',
        'nomor_telepon',
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
     * Scope untuk filter berdasarkan kecamatan
     * ✅ DIPERBAIKI: cluster → kecamatan
     */
    public function scopeByKecamatan($query, $kecamatan)
    {
        return $query->where('kecamatan', $kecamatan);
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
     * ✅ DITAMBAHKAN: kecamatan dalam alamat lengkap
     */
    public function getAlamatLengkapAttribute()
    {
        $parts = array_filter([
            $this->alamat,
            $this->kecamatan,
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

    /**
     * Accessor untuk lokasi wilayah (Provinsi → Kabupaten → Kecamatan)
     * ✅ BARU: Menampilkan hierarki wilayah lengkap
     */
    public function getWilayahLengkapAttribute()
    {
        $parts = array_filter([
            $this->provinsi,
            $this->kabupaten,
            $this->kecamatan
        ]);

        return implode(' → ', $parts);
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
     * @param string|null $provinsi
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
     * Hitung total pelanggan per kecamatan
     * ✅ DIPERBAIKI: totalPerCluster → totalPerKecamatan
     *
     * @param string|null $kabupaten
     * @return \Illuminate\Support\Collection
     */
    public static function totalPerKecamatan($kabupaten = null)
    {
        $query = self::select('kecamatan', DB::raw('COUNT(*) as total'))
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '');

        if ($kabupaten) {
            $query->where('kabupaten', $kabupaten);
        }

        return $query->groupBy('kecamatan')
                    ->orderByDesc('total')
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
        $now = Carbon::now();

        // Loop 12 bulan terakhir (dari 11 bulan lalu sampai bulan ini)
        for ($i = 11; $i >= 0; $i--) {
            // Buat Carbon instance baru untuk setiap iterasi
            $bulanIni = Carbon::create($now->year, $now->month, 1)->subMonths($i);

            // Ambil akhir bulan untuk query (instance terpisah)
            $akhirBulan = Carbon::create($bulanIni->year, $bulanIni->month, 1)
                                ->endOfMonth()
                                ->endOfDay();

            // Hitung TOTAL AKUMULATIF pelanggan sampai akhir bulan ini
            $totalAkumulatif = self::where('created_at', '<=', $akhirBulan)->count();

            // Simpan hasil
            $hasil[] = [
                'label' => $bulanIni->translatedFormat('M'),  // Jan, Feb, Mar, dst
                'jumlah' => $totalAkumulatif,
                'bulan' => $bulanIni->month,
                'tahun' => $bulanIni->year
            ];
        }

        return collect($hasil);
    }

    /**
     * Get statistik total pelanggan
     * ✅ DIPERBAIKI: per_cluster → per_kecamatan
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
            'per_kabupaten' => self::totalPerKabupaten(),
            'per_kecamatan' => self::totalPerKecamatan(),
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

    /**
     * Get statistik wilayah (Provinsi → Kabupaten → Kecamatan)
     * ✅ BARU: Method untuk analisis hierarki wilayah
     *
     * @param string|null $provinsi
     * @param string|null $kabupaten
     * @return array
     */
    public static function statistikWilayah($provinsi = null, $kabupaten = null)
    {
        $query = self::query();

        if ($provinsi) {
            $query->where('provinsi', $provinsi);
        }

        if ($kabupaten) {
            $query->where('kabupaten', $kabupaten);
        }

        return [
            'total' => $query->count(),
            'per_provinsi' => self::totalPerProvinsi(),
            'per_kabupaten' => self::totalPerKabupaten($provinsi),
            'per_kecamatan' => self::totalPerKecamatan($kabupaten),
        ];
    }

    /**
     * Get list kecamatan berdasarkan provinsi dan kabupaten
     * ✅ BARU: Method untuk dropdown cascade
     *
     * @param string $provinsi
     * @param string $kabupaten
     * @return \Illuminate\Support\Collection
     */
    public static function getKecamatanList($provinsi, $kabupaten)
    {
        return self::select('kecamatan')
            ->where('provinsi', $provinsi)
            ->where('kabupaten', $kabupaten)
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
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
