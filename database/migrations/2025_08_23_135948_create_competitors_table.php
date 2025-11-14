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
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            
            // ✅ Foreign Key ke users table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // ✅ Data Sales
            $table->string('sales_name');
            
            // ✅ Data Competitor
            $table->string('cluster');
            $table->string('competitor_name');
            $table->string('paket')->nullable();
            $table->string('kecepatan')->nullable();
            $table->string('kuota')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->string('fitur_tambahan')->nullable();
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // ✅ Index untuk performa query
            $table->index('user_id');
            $table->index('cluster');
            $table->index('sales_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitors');
    }
};