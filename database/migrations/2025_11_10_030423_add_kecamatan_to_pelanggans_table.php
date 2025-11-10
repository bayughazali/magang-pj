<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah kolom sudah ada
        if (!Schema::hasColumn('pelanggans', 'kecamatan')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                $table->string('kecamatan', 100)->nullable()->after('kabupaten');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pelanggans', 'kecamatan')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                $table->dropColumn('kecamatan');
            });
        }
    }
};