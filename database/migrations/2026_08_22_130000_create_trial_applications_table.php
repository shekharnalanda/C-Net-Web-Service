<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trial_applications', function (Blueprint $table) {
            $table->id();
            $table->string('business_name', 150);
            $table->string('owner_name', 120);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('desired_slug', 100)->unique();
            $table->string('category', 120);
            $table->string('tagline', 250)->nullable();
            $table->text('about_business');
            $table->text('services_offered')->nullable();
            $table->text('address')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('theme_color', 20)->default('#0756a3');
            $table->string('status', 30)->default('pending');
            $table->string('trial_url', 255)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_applications');
    }
};
