<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_name',
        'user_id',
        'cluster',
        'competitor_name',
        'paket',
        'kecepatan',
        'kuota',
        'harga',
        'fitur_tambahan',
        'keterangan',
    ];

    /**
     * Relasi ke User
     * ✅ TAMBAHKAN INI
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}