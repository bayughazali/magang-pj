<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetitorController;
use App\Http\Controllers\OperationalReportController;
use App\Http\Controllers\ReportActivityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CustomerSearchController;

// ================== ROOT REDIRECT ================== //
Route::get('/', function () {
    return redirect()->route('login');
});

// ================== AUTH ROUTES (PUBLIC) ================== //
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot password
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot.password');
Route::post('/send-reset-code', [AuthController::class, 'sendResetCode'])->name('send.reset.code');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('reset.password');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password.post');

// ================== PROTECTED ROUTES (REQUIRE AUTH) ================== //
Route::middleware('auth')->group(function () {

    // ================== DASHBOARD ROUTES ================== //
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role-based dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('role:admin')->name('admin.dashboard');

    Route::get('/user/dashboard', function () {
        return view('dashboard');

        // route user
    })->middleware('role:user')->name('user.dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // ================== REPORT ACTIVITY ROUTES ================== //
    Route::prefix('reports')->name('reports.')->group(function () {

        // Report Activity CRUD
        Route::get('/activity', [ReportActivityController::class, 'index'])->name('activity');
        Route::post('/activity', [ReportActivityController::class, 'store'])->name('store');
        Route::get('/activity/{id}/edit', [ReportActivityController::class, 'edit'])->name('edit');
        Route::put('/activity/{id}', [ReportActivityController::class, 'update'])->name('update');
        Route::delete('/activity/{id}', [ReportActivityController::class, 'destroy'])->name('destroy');

        // Export routes
        Route::get('/activity/export-pdf', [ReportActivityController::class, 'exportPdf'])->name('exportPdf');
        Route::get('/activity/export-csv', [ReportActivityController::class, 'exportCsv'])->name('exportCsv');
        Route::get('/activity/export', [ReportActivityController::class, 'export'])->name('export');
        Route::get('/activity/print', [ReportActivityController::class, 'printView'])->name('print');

        // Contoh route yang benar
Route::get('/report-activity/export-pdf', [ReportActivityController::class, 'exportPdf'])->name('report.activity.pdf');

// Atau jika menggunakan resource route:
Route::resource('report-activity', ReportActivityController::class);
// Tambahkan route khusus untuk export
Route::get('/report-activity/export-pdf', [ReportActivityController::class, 'exportPdf']);

        // Report Competitor (view saja) - menggunakan ReportController
        Route::get('/competitor', [ReportController::class, 'competitor'])->name('competitor');

        // Other report routes (jika ada)
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');

        // Debug routes (hapus setelah masalah selesai)
        Route::get('/debug', [ReportController::class, 'debugData'])->name('debug');
        Route::get('/refresh', [ReportController::class, 'refresh'])->name('refresh');
    });

    // ================== COMPETITOR ROUTES ================== //
    Route::resource('competitor', CompetitorController::class);
// ================== OPERATIONAL REPORT ROUTES ================== //
// Route untuk Input Data Pelanggan
Route::prefix('report/operational')->name('report.operational.')->group(function () {
    Route::get('/', [OperationalReportController::class, 'index'])->name('index');
    Route::post('/', [OperationalReportController::class, 'store'])->name('store');
    Route::get('/show', [OperationalReportController::class, 'show'])->name('show');
    Route::put('/{pelanggan}', [OperationalReportController::class, 'update'])->name('update');
    Route::delete('/{pelanggan}', [OperationalReportController::class, 'destroy'])->name('destroy');

    // ========== MISSING API ENDPOINTS - ADD THESE ========== //
    Route::get('/get-kabupaten', [OperationalReportController::class, 'getKabupaten'])->name('get-kabupaten');
    Route::get('/get-kode-fat', [OperationalReportController::class, 'getKodeFat'])->name('get-kode-fat');
});
    // Route::get('/customer/search', function() {
    //     return view('customer.search');
    // })->name('customer.search');

// Perbaiki route pertama (typo: repot -> report)
Route::get('/report/customer/search', function() {
    return view('report.customer.search'); // Buat view ini jika belum ada
})->name('report.customer.search');

// Route untuk Customer dengan middleware auth
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    // Route untuk search customer
    Route::get('/search', [App\Http\Controllers\CustomerSearchController::class, 'index'])->name('search');

    // Route untuk edit customer - perbaiki pattern URL
    Route::get('/{id}/edit', [App\Http\Controllers\CustomerSearchController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\CustomerSearchController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\CustomerSearchController::class, 'destroy'])->name('destroy');

    // Map and Location Routes - perbaiki URL pattern
    Route::get('/map', [App\Http\Controllers\CustomerSearchController::class, 'showMap'])->name('map');

    // API Routes for dropdown data
    Route::get('/api/provinsi', [App\Http\Controllers\CustomerSearchController::class, 'getProvinsi'])->name('api.provinsi');
    Route::get('/api/kabupaten', [App\Http\Controllers\CustomerSearchController::class, 'getKabupaten'])->name('api.kabupaten');
    Route::get('/api/statistics', [App\Http\Controllers\CustomerSearchController::class, 'getStatistics'])->name('api.statistics');

    Route::get('/search/fat', [App\Http\Controllers\CustomerSearchController::class, 'searchByFAT'])->name('search.fat');
    Route::get('/search/advanced', [App\Http\Controllers\CustomerSearchController::class, 'advancedSearch'])->name('search.advanced');
    Route::get('/statistics', [App\Http\Controllers\CustomerSearchController::class, 'getStatistics'])->name('statistics');
    Route::post('/export', [App\Http\Controllers\CustomerSearchController::class, 'exportSearch'])->name('export');

        // Route tambahan untuk fitur advanced
        Route::get('/search/fat', [App\Http\Controllers\CustomerSearchController::class, 'searchByFAT'])->name('search.fat');
        Route::post('/search/advanced', [App\Http\Controllers\CustomerSearchController::class, 'advancedSearch'])->name('search.advanced');
        Route::get('/export', [App\Http\Controllers\CustomerSearchController::class, 'exportSearch'])->name('export');
        Route::get('/statistics', [App\Http\Controllers\CustomerSearchController::class, 'getStatistics'])->name('statistics');
    });

        // ================== OTHER UTILITY ROUTES ================== //
        Route::get('/debug-images', [ReportController::class, 'debugImages'])->name('debug.images');
        Route::get('/debug-storage', [ReportActivityController::class, 'debugStorage']);
        Route::get('/fix-storage', [ReportActivityController::class, 'fixStorage']);
    });
// Existing routes
Route::get('/report/operational', [OperationalReportController::class, 'index'])->name('report.operational.index');
Route::post('/report/operational', [OperationalReportController::class, 'store'])->name('report.operational.store');
Route::put('/report/operational/{pelanggan}', [OperationalReportController::class, 'update'])->name('report.operational.update');
Route::delete('/report/operational/{pelanggan}', [OperationalReportController::class, 'destroy'])->name('report.operational.destroy');

// New route for AJAX call
Route::get('/operational/get-kecepatan', [OperationalReportController::class, 'getKecepatanByCluster'])->name('operational.getKecepatanByCluster');

// Competitor routes
Route::get('/competitor', [CompetitorController::class, 'index'])->name('competitor.index');
Route::post('/competitor', [CompetitorController::class, 'store'])->name('competitor.store');
Route::get('/competitor/{id}/edit', [CompetitorController::class, 'edit'])->name('competitor.edit');
Route::put('/competitor/{id}', [CompetitorController::class, 'update'])->name('competitor.update');
Route::delete('/competitor/{id}', [CompetitorController::class, 'destroy'])->name('competitor.destroy');
// ================== FALLBACK ROUTE ================== //
Route::fallback(function () {
    return redirect()->route('login');
});
