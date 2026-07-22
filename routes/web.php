<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Storage Link Route (for Hostinger setup)
Route::get('/linkstorage', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Storage Link Created Successfully';
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Profile
    Route::get('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications (shared by Admin & MR)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('index');
        Route::get('/feed', [\App\Http\Controllers\Web\NotificationController::class, 'feed'])->name('feed');
        Route::post('/read-all', [\App\Http\Controllers\Web\NotificationController::class, 'readAll'])->name('read-all');
        Route::get('/{id}/read', [\App\Http\Controllers\Web\NotificationController::class, 'read'])->name('read');
    });
    
    // Admin Routes
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        
        // User Management
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', UserController::class);

        // Product Management
        Route::post('products/{product}/toggle-status', [\App\Http\Controllers\Web\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::resource('products', \App\Http\Controllers\Web\ProductController::class);

        // Sample Assignment Management
        Route::get('samples/adjust/{user}/{product}', [\App\Http\Controllers\Web\SampleAssignmentController::class, 'adjustForm'])->name('samples.adjust.form');
        Route::post('samples/adjust', [\App\Http\Controllers\Web\SampleAssignmentController::class, 'adjust'])->name('samples.adjust');
        Route::resource('samples', \App\Http\Controllers\Web\SampleAssignmentController::class)->only(['index', 'show', 'create', 'store']);

        // Attendance Management
        Route::resource('attendance', \App\Http\Controllers\Web\AttendanceController::class)->only(['index', 'show']);

        // Doctor Visits
        Route::resource('visits', \App\Http\Controllers\Web\DoctorVisitController::class)->only(['index', 'show']);

        // Orders Management
        Route::resource('orders', \App\Http\Controllers\Web\OrderController::class)->only(['index', 'show']);
        Route::post('orders/{order}/status', [\App\Http\Controllers\Web\OrderController::class, 'updateStatus'])->name('orders.status');

        // Daily Reports
        Route::resource('daily-reports', \App\Http\Controllers\Web\DailyReportController::class)->only(['index', 'show']);
        Route::post('daily-reports/{report}/review', [\App\Http\Controllers\Web\DailyReportController::class, 'markReviewed'])->name('daily-reports.review');

        // Reports Hub (Analytics)
        Route::prefix('reports')->name('reports.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Web\Admin\Reports\ReportHubController::class, 'index'])->name('hub');
            Route::get('/attendance', [\App\Http\Controllers\Web\Admin\Reports\AttendanceReportController::class, 'index'])->name('attendance');
            Route::get('/visits', [\App\Http\Controllers\Web\Admin\Reports\VisitReportController::class, 'index'])->name('visits');
            Route::get('/orders', [\App\Http\Controllers\Web\Admin\Reports\OrderReportController::class, 'index'])->name('orders');
            Route::get('/samples', [\App\Http\Controllers\Web\Admin\Reports\SampleReportController::class, 'index'])->name('samples');
            Route::get('/performance', [\App\Http\Controllers\Web\Admin\Reports\MrPerformanceReportController::class, 'index'])->name('performance');
        });
        // Settings Management
        Route::get('/settings', [\App\Http\Controllers\Web\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Web\Admin\SettingController::class, 'store'])->name('settings.store');

        // Activity Logs
        Route::get('/logs/timeline', [\App\Http\Controllers\Web\Admin\ActivityLogController::class, 'timeline'])->name('logs.timeline');
        Route::resource('logs', \App\Http\Controllers\Web\Admin\ActivityLogController::class)->only(['index', 'show']);
    });
    
    // MR Routes
    Route::middleware(['role:MR'])->prefix('mr')->name('mr.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mr'])->name('dashboard');
        
        // MR Attendance
        Route::get('/attendance', [\App\Http\Controllers\Web\Mr\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/mark', [\App\Http\Controllers\Web\Mr\AttendanceController::class, 'markForm'])->name('attendance.mark');
        Route::post('/attendance/check-in', [\App\Http\Controllers\Web\Mr\AttendanceController::class, 'checkIn'])->name('attendance.checkin');
        Route::post('/attendance/check-out', [\App\Http\Controllers\Web\Mr\AttendanceController::class, 'checkOut'])->name('attendance.checkout');
        
        // MR Doctor Visits
        Route::get('/visits', [\App\Http\Controllers\Web\Mr\DoctorVisitController::class, 'index'])->name('visits.index');
        Route::get('/visits/create', [\App\Http\Controllers\Web\Mr\DoctorVisitController::class, 'create'])->name('visits.create');
        Route::post('/visits', [\App\Http\Controllers\Web\Mr\DoctorVisitController::class, 'store'])->name('visits.store');

        // MR Orders
        Route::resource('orders', \App\Http\Controllers\Web\Mr\OrderController::class)->only(['index', 'show']);

        // MR Daily Reports
        Route::get('/reports', [\App\Http\Controllers\Web\Mr\DailyReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [\App\Http\Controllers\Web\Mr\DailyReportController::class, 'createOrEdit'])->name('reports.create');
        Route::post('/reports', [\App\Http\Controllers\Web\Mr\DailyReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [\App\Http\Controllers\Web\Mr\DailyReportController::class, 'show'])->name('reports.show');

        // MR Samples
        Route::get('/samples', [\App\Http\Controllers\Web\Mr\SampleController::class, 'index'])->name('samples.index');
    });
});
