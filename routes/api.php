<?php

use App\Http\Controllers\Api\Mobile\PublicController;
use App\Http\Controllers\Api\Mobile\ClientAuthController;
use App\Http\Controllers\Api\Mobile\ClientController;
use App\Http\Controllers\Api\Mobile\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile/v1')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('/health', [PublicController::class, 'health']);
        Route::get('/dashboard', [PublicController::class, 'dashboard']);
        Route::get('/services', [PublicController::class, 'services']);
        Route::get('/plans', [PublicController::class, 'plans']);

        Route::post('/client/request-otp', [ClientAuthController::class, 'requestOtp'])
            ->middleware('throttle:3,10');
        Route::post('/client/verify-otp', [ClientAuthController::class, 'verifyOtp'])
            ->middleware('throttle:6,10');
        Route::post('/client/logout', [ClientAuthController::class, 'logout']);
        Route::get('/client/me', [ClientController::class, 'me']);

        Route::post('/admin/login', [AdminController::class, 'login'])
            ->middleware('throttle:5,1');
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
        Route::patch('/admin/enquiries/{id}', [AdminController::class, 'updateEnquiry']);
        Route::post('/admin/logout', [AdminController::class, 'logout']);
    });
