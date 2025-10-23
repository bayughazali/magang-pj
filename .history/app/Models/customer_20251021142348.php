<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'pelanggans';

    /**
     * Primary key
     */
    protected $primaryKey = 'id';

    /**
     * Primary key type
     */
    protected $keyType = 'int';

    /**
     * Auto increment
     */
    public $incrementing = true;

    /**
     * Timestamps
     */
    public $timestamps = true;

    /**
     * Fillable fields
     */
    protected $fillable = [
        'id_pelanggan',
        'nama_pelanggan',
        'bandwidth',
        'alamat',
        'provinsi',
        'kabupaten',
        'latitude',
        'longitude',
        'nomor_telepon',
        'cluster',
        'kode_fat',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // PENTING: Jangan gunakan SoftDeletes kecuali memang diperlukan
    // use SoftDeletes; // COMMENT ATAU HAPUS BARIS INI
}
