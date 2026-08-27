<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_login_otps', function (Blueprint $table): void {
            $table->id();
            $table->string('email', 150)->index();
            $table->string('otp_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mobile_client_tokens', function (Blueprint $table): void {
            $table->id();
            $table->string('email', 150)->index();
            $table->string('token_hash', 64)->unique();
            $table->string('device_name', 150)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_client_tokens');
        Schema::dropIfExists('mobile_login_otps');
    }
};
