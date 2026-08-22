<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('trials:expire', function () {
    $expired = DB::table('trial_applications')
        ->where('status', 'approved')
        ->whereNotNull('expires_at')
        ->where('expires_at', '<', now())
        ->update([
            'status' => 'expired',
            'updated_at' => now(),
        ]);

    $this->info("Expired trial websites: {$expired}");
})->purpose('Expire trial websites after their validity period');

Schedule::command('trials:expire')->hourly();
