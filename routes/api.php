<?php

use App\Http\Controllers\Api\Mobile\PublicController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile/v1')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('/health', [PublicController::class, 'health']);
        Route::get('/dashboard', [PublicController::class, 'dashboard']);
        Route::get('/services', [PublicController::class, 'services']);
        Route::get('/plans', [PublicController::class, 'plans']);
    });
