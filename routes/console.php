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
            'expired_at' => now(),
            'updated_at' => now(),
        ]);

    $this->info("Expired trial websites: {$expired}");
})->purpose('Disable trial websites after seven days');

Artisan::command('trials:purge', function () {
    $deleted = DB::table('trial_applications')
        ->whereIn('status', ['expired', 'rejected'])
        ->where(function ($query) {
            $query
                ->where(
                    'expired_at',
                    '<=',
                    now()->subDays(30)
                )
                ->orWhere(
                    'updated_at',
                    '<=',
                    now()->subDays(30)
                );
        })
        ->delete();

    $this->info("Permanently deleted old trial records: {$deleted}");
})->purpose('Delete expired or rejected trial records after 30 days');

Schedule::command('trials:expire')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('trials:purge')
    ->dailyAt('02:15')
    ->withoutOverlapping();
