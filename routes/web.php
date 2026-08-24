<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TrialController;
use Illuminate\Support\Facades\Route;

Route::domain('web.mciedu.com')->get('/', function () {
    return view('home');
});

Route::domain('{slug}.mciedu.com')->get('/', [TrialController::class, 'show'])
    ->name('trial.subdomain');

Route::domain('{slug}.web.mciedu.com')->get('/', [TrialController::class, 'show'])
    ->name('trial.subdomain.legacy');
Route::view('/', 'home')->name('home');
Route::redirect('/login', '/admin/login')->name('login');

Route::get('/enquiry', [EnquiryController::class, 'create'])->name('enquiry.create');
Route::post('/enquiry', [EnquiryController::class, 'store'])->middleware('throttle:10,1')->name('enquiry.store');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/plans', [PlanController::class, 'publicIndex'])->name('plans');

Route::get('/trial/apply', [TrialController::class, 'create'])->name('trial.apply');
Route::post('/trial/apply', [TrialController::class, 'store'])->middleware('throttle:3,10')->name('trial.store');
Route::get('/trial/{slug}', [TrialController::class, 'show'])->name('trial.show');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/admin/login', [AdminController::class, 'authenticate'])->middleware('throttle:5,1')->name('admin.authenticate');
});

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/change-password', [AdminController::class, 'changePassword'])->name('admin.password.edit');
    Route::put('/change-password', [AdminController::class, 'updatePassword'])->name('admin.password.update');

    Route::get('/services', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');

    Route::get('/plans', [PlanController::class, 'index'])->name('admin.plans.index');
    Route::get('/plans/create', [PlanController::class, 'create'])->name('admin.plans.create');
    Route::post('/plans', [PlanController::class, 'store'])->name('admin.plans.store');
    Route::get('/plans/{id}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
    Route::put('/plans/{id}', [PlanController::class, 'update'])->name('admin.plans.update');
    Route::delete('/plans/{id}', [PlanController::class, 'destroy'])->name('admin.plans.destroy');

    Route::get('/trials', [TrialController::class, 'index'])->name('admin.trials.index');
    Route::patch('/trials/{id}/status', [TrialController::class, 'updateStatus'])->name('admin.trials.status');
    Route::post('/trials/{id}/extend', [TrialController::class, 'extend'])->name('admin.trials.extend');
    Route::post('/trials/{id}/upgrade', [TrialController::class, 'upgrade'])->name('admin.trials.upgrade');
    Route::post('/trials/{id}/suspend', [TrialController::class, 'suspend'])->name('admin.trials.suspend');
    Route::post('/trials/{id}/restore', [TrialController::class, 'restore'])->name('admin.trials.restore');
    Route::delete('/trials/{id}', [TrialController::class, 'destroy'])->name('admin.trials.destroy');

    Route::patch('/enquiries/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.enquiries.status');
    Route::delete('/enquiries/{id}', [AdminController::class, 'destroy'])->name('admin.enquiries.delete');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
});

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/projects', [\App\Http\Controllers\WebsiteProjectController::class, 'index'])
        ->name('admin.projects.index');

    Route::get('/trials/{trialId}/convert', [\App\Http\Controllers\WebsiteProjectController::class, 'create'])
        ->name('admin.projects.convert');

    Route::post('/trials/{trialId}/convert', [\App\Http\Controllers\WebsiteProjectController::class, 'store'])
        ->name('admin.projects.store');

    Route::get('/projects/{id}', [\App\Http\Controllers\WebsiteProjectController::class, 'show'])
        ->name('admin.projects.show');

    Route::patch('/projects/{id}', [\App\Http\Controllers\WebsiteProjectController::class, 'update'])
        ->name('admin.projects.update');
});
