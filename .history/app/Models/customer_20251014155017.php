<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Customer extends Model
{
     protected $table = 'pelanggans'; // ← ganti sesuai nama tabel kamu

    protected $fillable = [
        'nama_pelanggan',
        'alamat',
        'telepon',
        'cluster',
        // kolom lain sesuai struktur tabelmu
    ];
}
