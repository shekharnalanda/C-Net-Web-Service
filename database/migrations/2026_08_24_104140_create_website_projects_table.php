<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trial_application_id')
                ->nullable()
                ->unique()
                ->constrained('trial_applications')
                ->nullOnDelete();

            $table->foreignId('plan_id')
                ->nullable()
                ->constrained('plans')
                ->nullOnDelete();

            $table->string('project_name', 180);
            $table->string('project_type', 40);
            $table->string('custom_domain', 190)->unique();

            $table->text('requirements')->nullable();

            $table->decimal('quoted_amount', 12, 2)->nullable();
            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->string('payment_status', 30)->default('pending');
            $table->string('project_status', 40)->default('planning');

            $table->longText('deployment_checklist')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamp('converted_at')->nullable();
            $table->timestamp('launched_at')->nullable();

            $table->timestamps();

            $table->index('project_type');
            $table->index('payment_status');
            $table->index('project_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_projects');
    }
};
