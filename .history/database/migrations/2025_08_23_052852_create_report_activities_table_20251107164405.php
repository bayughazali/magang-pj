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
            $table->enum('cluster', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'])->nullable(); // hilangkan ->after('lokasi')
            $table->string('evidence')->nullable(); // untuk foto progress
            $table->text('hasil_kendala')->nullable();
            $table->enum('status', ['Selesai', 'Proses'])->default('Proses');
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
