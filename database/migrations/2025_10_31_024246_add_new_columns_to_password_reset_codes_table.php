<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('password_reset_codes', function (Blueprint $table) {
            // Tambahkan kolom baru di sini (jika perlu)
            if (!Schema::hasColumn('password_reset_codes', 'status')) {
                $table->string('status')->default('pending');
            }

            if (!Schema::hasColumn('password_reset_codes', 'expires_at')) {
                $table->timestamp('expires_at')->nullable();
            }

            if (!Schema::hasColumn('password_reset_codes', 'used_at')) {
                $table->timestamp('used_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_codes', function (Blueprint $table) {
            $table->dropColumn(['status', 'expires_at', 'used_at']);
        });
    }
};