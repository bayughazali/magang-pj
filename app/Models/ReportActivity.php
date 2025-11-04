<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportActivity extends Model
{
    use HasFactory;

    // 🔹 Pastikan nama tabel sesuai dengan database
    protected $table = 'report_activities'; // ✅ Tambahkan ini jika tabelnya tidak jamak otomatis


    protected $fillable = [
    'sales',
    'aktivitas',
    'tanggal',
    'lokasi',
    'cluster',
    'evidence',
    'hasil_kendala',
    'status',
    'nominal',        // ✅ tambahkan kolom nominal agar bisa dihitung di dashboard
];

 // 🔹 Jika tanggal disimpan dalam kolom `tanggal`, Laravel bisa otomatis casting-nya jadi Carbon
    protected $casts = [
        'tanggal' => 'datetime',
    ];
}
