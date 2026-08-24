<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trial_applications', function (Blueprint $table) {
            $table->string('website_name', 150)->nullable();
            $table->string('template_key', 30)->default('modern');
            $table->timestamp('consent_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('expired_at')->nullable();
        });

        DB::table('trial_applications')
            ->whereNull('website_name')
            ->update([
                'website_name' => DB::raw('business_name'),
            ]);
    }

    public function down(): void
    {
        Schema::table('trial_applications', function (Blueprint $table) {
            $table->dropColumn([
                'website_name',
                'template_key',
                'consent_at',
                'suspended_at',
                'expired_at',
            ]);
        });
    }
};
