<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Cek apakah kolom sudah ada
            if (!Schema::hasColumn('pelanggans', 'sales_name')) {
                $table->string('sales_name')->nullable()->after('id_pelanggan');
            }
            
            if (!Schema::hasColumn('pelanggans', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('sales_name');
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            if (Schema::hasColumn('pelanggans', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('pelanggans', 'sales_name')) {
                $table->dropColumn('sales_name');
            }
        });
    }
};