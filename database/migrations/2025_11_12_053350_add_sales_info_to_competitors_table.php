<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('competitors', function (Blueprint $table) {
            $table->string('sales_name')->nullable()->after('id');
            $table->unsignedBigInteger('user_id')->nullable()->after('sales_name');
            
            // Foreign key ke tabel users
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('competitors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['sales_name', 'user_id']);
        });
    }
};