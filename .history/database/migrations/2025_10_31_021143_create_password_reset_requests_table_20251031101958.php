<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('code', 6); // Kode 6 digit
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('code');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_reset_requests');
    }
};
