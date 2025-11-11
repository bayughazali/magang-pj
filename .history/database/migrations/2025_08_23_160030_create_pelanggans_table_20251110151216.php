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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->string('id_pelanggan', 20)->primary(); // CUST-0001
            $table->string('nama_pelanggan', 100);
            $table->string('bandwidth', 20);
            $table->string('nomor_telepon', 20);
            $table->string('provinsi', 50)->nullable();
            $table->string('kabupaten', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->text('alamat');
            $table->string('cluster', 20);
            $table->string('kode_fat', 50)->nullable();
            $table->decimal('latitude', 10, 6)->default(-8.409518);
            $table->decimal('longitude', 10, 6)->default(115.188916);
            $table->timestamps();

            // Index untuk performa query
            $table->index('provinsi');
            $table->index('kabupaten');
            $table->index('cluster');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};