<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';   // ✅ primary pakai id_pelanggan
    public $incrementing = false;             // ✅ non auto increment
    protected $keyType = 'string';            // ✅ tipe primary string
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

    // Scope untuk filter berdasarkan provinsi
    public function scopeByProvinsi($query, $provinsi)
    {
        return $query->where('provinsi', $provinsi);
    }

    // Scope untuk filter berdasarkan kabupaten
    public function scopeByKabupaten($query, $kabupaten)
    {
        return $query->where('kabupaten', $kabupaten);
    }

    // Scope untuk filter berdasarkan cluster
    public function scopeByCluster($query, $cluster)
    {
        return $query->where('cluster', $cluster);
    }

    // Accessor untuk format alamat lengkap
    public function getAlamatLengkapAttribute()
    {
        return $this->alamat . ', ' . $this->kabupaten . ', ' . $this->provinsi;
    }

    // Accessor untuk koordinat dalam format string
    public function getKoordinatAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return null;
    }

    // Method untuk mendapatkan URL Google Maps
    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }
}
