<?php

use App\Http\Controllers\TelescopeAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    abort(403);
});

Route::middleware(['throttle:12,1'])->group(function (): void {
    Route::get('/telescope-auth/login', [TelescopeAuthController::class, 'create'])
        ->name('telescope-auth.login');
    Route::post('/telescope-auth/login', [TelescopeAuthController::class, 'store'])
        ->middleware('throttle:8,1')
        ->name('telescope-auth.login.store');
});

Route::post('/telescope-auth/logout', [TelescopeAuthController::class, 'destroy'])
    ->middleware(['auth:web', 'throttle:12,1'])
    ->name('telescope-auth.logout');
