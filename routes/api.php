<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\PublicRequestController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\GateController;
use App\Http\Controllers\Api\AuditController;

// Public endpoint for visitor form submission
Route::post('/public/request', [PublicRequestController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Device Management
    Route::apiResource('devices', DeviceController::class);
    Route::get('/devices/{device}/logs', [DeviceController::class, 'getLogs']);
    Route::post('/devices/{device}/sync', [DeviceController::class, 'sync']);

    // Public Requests (Admin)
    Route::get('/requests', [PublicRequestController::class, 'index']);
    Route::get('/requests/{publicRequest}', [PublicRequestController::class, 'show']);

    // Approval Workflow
    Route::post('/requests/{publicRequest}/approve', [ApprovalController::class, 'approve']);
    Route::post('/requests/{publicRequest}/reject', [ApprovalController::class, 'reject']);
    Route::post('/requests/{publicRequest}/revoke', [ApprovalController::class, 'revoke']);

    // Gate Management
    Route::post('/gate/checkin', [GateController::class, 'checkIn']);
    Route::post('/gate/checkout', [GateController::class, 'checkOut']);
    Route::get('/gate/visitors/{approvedVisitor}/history', [GateController::class, 'getHistory']);
    Route::get('/gate/devices/{device}/active-visitors', [GateController::class, 'getActiveVisitors']);

    // Audit Logs
    Route::get('/audit/logs', [AuditController::class, 'index']);
    Route::get('/audit/report', [AuditController::class, 'report']);
    Route::get('/audit/resource-history', [AuditController::class, 'resourceHistory']);
});
