<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentFeeApiController;
use App\Http\Controllers\Users\Student\StudentController;

// Login (no session, no web, no throttle)
Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware('throttle:api');
Route::get('/app/version-check', [AuthController::class, 'versionCheck']);

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/student/homework', [StudentController::class, 'index']);
    Route::get('/student/attendance/monthly', [StudentController::class, 'attendanceMonthly']);
    Route::get('/student/profile', [StudentController::class, 'profile']);
    Route::get('/student/announcements', [StudentController::class, 'announcements']);
    Route::post('/student/reset-password', [StudentController::class, 'resetPasswordApi']);
    Route::post('/student/device-token', [AuthController::class, 'deviceToken']);
    Route::get('/student/receipts', [StudentFeeApiController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Debug
Route::get('/debug/hello', function () {
    return response()->json([
        'ok'   => true,
        'time' => now()->toDateTimeString(),
    ]);
});

Route::middleware('auth:sanctum')->post(
    '/student/fee/feeDetails',
    [StudentFeeApiController::class, 'feeDetailsApi']
);

Route::get(
    '/student/fee/dashboardSummary',
    [StudentFeeApiController::class, 'dashboardSummary']
);
