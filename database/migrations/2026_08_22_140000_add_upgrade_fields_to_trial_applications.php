<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trial_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('selected_plan_id')->nullable();
            $table->timestamp('upgraded_at')->nullable();
            $table->text('admin_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trial_applications', function (Blueprint $table) {
            $table->dropColumn(['selected_plan_id', 'upgraded_at', 'admin_notes']);
        });
    }
};
