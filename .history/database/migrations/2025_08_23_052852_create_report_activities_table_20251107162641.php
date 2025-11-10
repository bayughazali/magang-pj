<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_activities', function (Blueprint $table) {
            $table->id();
            $table->string('sales');
            $table->string('aktivitas');
            $table->date('tanggal');
            $table->string('lokasi');

            // Kolom wilayah baru (Provinsi, Kabupaten, Kecamatan)
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();

            // Kolom cluster lama - bisa dihapus atau dipertahankan untuk backward compatibility
            // $table->enum('cluster', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'])->nullable();

            $table->string('evidence')->nullable(); // untuk foto progress
            $table->text('hasil_kendala')->nullable();
            $table->enum('status', ['selesai', 'proses'])->default('proses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_activities');
    }
};
