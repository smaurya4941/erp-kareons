<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::prefix('v1')->group(function () {
    // Throttle login attempts to mitigate brute-force / credential-stuffing.
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::put('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::put('/profile/password', [\App\Http\Controllers\Api\ProfileController::class, 'updatePassword']);

        // Admin Only API Routes
        Route::middleware(['role:Admin'])->group(function () {
            Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
            Route::apiResource('users', UserController::class);

            Route::post('products/{product}/toggle-status', [\App\Http\Controllers\Api\ProductController::class, 'toggleStatus']);
            Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class);

            Route::post('samples/adjust', [\App\Http\Controllers\Api\SampleAssignmentController::class, 'adjust']);
            Route::apiResource('samples', \App\Http\Controllers\Api\SampleAssignmentController::class)->only(['index', 'show', 'store']);

            Route::apiResource('attendance', \App\Http\Controllers\Api\AttendanceController::class)->only(['index', 'show']);
            Route::apiResource('visits', \App\Http\Controllers\Api\DoctorVisitController::class)->only(['index', 'show']);
            
            // New Admin Endpoints
            Route::apiResource('orders', \App\Http\Controllers\Api\OrderController::class)->only(['index', 'show']);
            Route::patch('orders/{order}/status', [\App\Http\Controllers\Api\OrderController::class, 'updateStatus']);
            
            Route::get('daily-report/summary', [\App\Http\Controllers\Api\DailyReportController::class, 'summary']);
            Route::apiResource('daily-reports', \App\Http\Controllers\Api\DailyReportController::class)->only(['index', 'show']);
            
            Route::get('dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'summary']);
            Route::get('dashboard/charts', [\App\Http\Controllers\Api\DashboardController::class, 'charts']);
            Route::get('dashboard/recent-activities', [\App\Http\Controllers\Api\DashboardController::class, 'recentActivities']);
            
            Route::get('reports/{type}', [\App\Http\Controllers\Api\ReportController::class, 'generate']);
            
            Route::get('settings/company', [\App\Http\Controllers\Api\SettingController::class, 'company']);
            Route::put('settings/company', [\App\Http\Controllers\Api\SettingController::class, 'updateCompany']);
            
            Route::apiResource('activity-logs', \App\Http\Controllers\Api\ActivityLogController::class)->only(['index', 'show']);
        });

        // MR Only API Routes
        Route::middleware(['role:MR'])->prefix('mr')->group(function () {
            Route::get('/samples', [\App\Http\Controllers\Api\Mr\SampleController::class, 'index']);
            
            Route::get('/attendance', [\App\Http\Controllers\Api\Mr\AttendanceController::class, 'index']);
            Route::get('/attendance/today', [\App\Http\Controllers\Api\Mr\AttendanceController::class, 'today']);
            Route::post('/attendance/check-in', [\App\Http\Controllers\Api\Mr\AttendanceController::class, 'checkIn']);
            Route::post('/attendance/check-out', [\App\Http\Controllers\Api\Mr\AttendanceController::class, 'checkOut']);
            
            Route::get('/visits', [\App\Http\Controllers\Api\Mr\DoctorVisitController::class, 'index']);
            Route::get('/visits/{doctorVisit}', [\App\Http\Controllers\Api\Mr\DoctorVisitController::class, 'show']);
            Route::post('/visits', [\App\Http\Controllers\Api\Mr\DoctorVisitController::class, 'store']);

            Route::post('/product-discussions', [\App\Http\Controllers\Api\Mr\ProductDiscussionController::class, 'store']);
            Route::get('/product-discussions/{visit}', [\App\Http\Controllers\Api\Mr\ProductDiscussionController::class, 'show']);

            Route::post('/sample-distributions', [\App\Http\Controllers\Api\Mr\SampleDistributionController::class, 'store']);
            Route::get('/sample-distributions', [\App\Http\Controllers\Api\Mr\SampleDistributionController::class, 'index']);

            Route::post('/orders', [\App\Http\Controllers\Api\Mr\OrderController::class, 'store']);
            Route::get('/orders', [\App\Http\Controllers\Api\Mr\OrderController::class, 'index']);
            Route::get('/orders/{order}', [\App\Http\Controllers\Api\Mr\OrderController::class, 'show']);

            Route::post('/daily-report', [\App\Http\Controllers\Api\Mr\DailyReportController::class, 'store']);
            Route::get('/daily-report/history', [\App\Http\Controllers\Api\Mr\DailyReportController::class, 'index']);
        });
    });
});
