<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MobileUserApprovalController;
use App\Http\Controllers\Admin\MovementHistoryController;
use App\Http\Controllers\Admin\QrController;
use App\Http\Controllers\Admin\TrolleyController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\DriverController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

Route::middleware('guest:admin')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth:admin')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('admin.logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/realtime', [DashboardController::class, 'realtime'])->name('admin.dashboard.realtime');
    Route::get('/history', [MovementHistoryController::class, 'index'])->name('admin.history.index');
    Route::get('/history/export', [MovementHistoryController::class, 'export'])->name('admin.history.export');
    Route::get('/history/refresh', [MovementHistoryController::class, 'refresh'])->name('admin.history.refresh');

    Route::get('/approvals', [MobileUserApprovalController::class, 'index'])->name('admin.approvals.index');
    Route::get('/approvals/{mobileUser}', [MobileUserApprovalController::class, 'show'])->name('admin.approvals.show');
    Route::post('/approvals/{mobileUser}/approve', [MobileUserApprovalController::class, 'approve'])->name('admin.approvals.approve');
    Route::post('/approvals/{mobileUser}/reject', [MobileUserApprovalController::class, 'reject'])->name('admin.approvals.reject');

    Route::get('/trolleys/print', [TrolleyController::class, 'print'])->name('trolleys.print');
    Route::get('/trolleys/export', [TrolleyController::class, 'export'])->name('trolleys.export');
    Route::get('/trolleys/export/xlsx', [TrolleyController::class, 'exportXlsx'])->name('trolleys.export.xlsx');
    Route::get('/qr-codes', [QrController::class, 'index'])->name('trolleys.qr.index');
    Route::resource('trolleys', TrolleyController::class)->except(['show']);
    Route::resource('vehicles', VehicleController::class)->except(['show']);
    Route::resource('drivers', DriverController::class)->except(['show']);
});
