<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TrolleyController;
use App\Http\Controllers\Api\TrolleyMovementController;
use App\Http\Controllers\Api\VehicleController as ApiVehicleController;
use App\Http\Controllers\Api\DriverController as ApiDriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:mobile')->group(function (): void {
        Route::get('trolleys', [TrolleyController::class, 'index']);
        Route::post('trolleys/{trolley}/checkout', [TrolleyMovementController::class, 'checkout']);
        Route::post('trolleys/{trolley}/checkin', [TrolleyMovementController::class, 'checkin']);
        Route::get('trolleys/{trolley}/history', [TrolleyMovementController::class, 'history']);
        Route::get('dashboard/summary', [DashboardController::class, 'mobileSummary']);
        Route::get('vehicles', [ApiVehicleController::class, 'index']);
        Route::get('drivers', [ApiDriverController::class, 'index']);
    });
});
