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

            // [DIUBAH] Mengganti enum menjadi string agar bisa menyimpan data lokasi yang panjang
            $table->string('cluster');

            $table->string('evidence')->nullable(); // untuk foto progress
            $table->text('hasil_kendala')->nullable();

            // [DIUBAH] Mengganti ke lowercase agar sesuai dengan validasi controller ('selesai', 'proses')
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
