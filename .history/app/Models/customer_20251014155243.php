<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Customer extends Model
{
    protected $table = 'pelanggans'; // ← ganti sesuai nama tabel kamu

    protected $fillable = [
        'id',
        'id_pelanggan',
        'nama_pelanggan',
        'bandwith',
        'alamat',
        'provinsi',
        'kabupaten',
        'latitude',
        'longtitude',
        
        // kolom lain sesuai struktur tabelmu
    ];
}
