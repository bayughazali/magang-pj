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
use App\Http\Controllers\CustomerSearchController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExportCompetitorController;
use App\Http\Controllers\ExportActivityController;


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
    })->middleware('role:user')->name('user.dashboard');

    // ================== USER MANAGEMENT ROUTES ================== //
    // PERBAIKAN: Hapus duplicated user resource routes dan gunakan satu definisi saja
    Route::resource('users', UserController::class);

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

        // Report Activity PDF Export - PERBAIKAN: Buat route yang konsisten
        Route::get('/report-activity/export-pdf', [ReportActivityController::class, 'exportPdf'])->name('report.activity.pdf');

        // Report Competitor (view saja) - menggunakan ReportController
        Route::get('/competitor', [ReportController::class, 'competitor'])->name('competitor');

        // Other report routes
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');

        // Debug routes (hapus setelah masalah selesai)
        Route::get('/debug', [ReportController::class, 'debugData'])->name('debug');
        Route::get('/refresh', [ReportController::class, 'refresh'])->name('refresh');
    });

    // ================== COMPETITOR ROUTES ================== //
    Route::resource('competitor', CompetitorController::class);

    // ================== OPERATIONAL REPORT ROUTES ================== //
    Route::prefix('report/operational')->name('report.operational.')->group(function () {
        Route::get('/', [OperationalReportController::class, 'index'])->name('index');
        Route::post('/', [OperationalReportController::class, 'store'])->name('store');
        Route::get('/show', [OperationalReportController::class, 'show'])->name('show');
        Route::put('/{pelanggan}', [OperationalReportController::class, 'update'])->name('update');
        Route::delete('/{pelanggan}', [OperationalReportController::class, 'destroy'])->name('destroy');

        // API endpoints
        Route::get('/get-kabupaten', [OperationalReportController::class, 'getKabupaten'])->name('get-kabupaten');
        Route::get('/get-kode-fat', [OperationalReportController::class, 'getKodeFat'])->name('get-kode-fat');
    });

    // ================== CUSTOMER ROUTES ================== //
    Route::get('/report/customer/search', function() {
        return view('report.customer.search');
    })->name('report.customer.search');

    Route::prefix('customer')->name('customer.')->group(function () {
        // Search customer routes
        Route::get('/search', [CustomerSearchController::class, 'index'])->name('search');
        Route::get('/{id}/edit', [CustomerSearchController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerSearchController::class, 'update'])->name('update');

        // PERBAIKAN: Route delete yang benar
        Route::delete('/{id}', [CustomerSearchController::class, 'destroy'])->name('destroy');

        // Map and Location Routes
        Route::get('/map', [CustomerSearchController::class, 'showMap'])->name('map');

        // API Routes for dropdown data
        Route::get('/api/provinsi', [CustomerSearchController::class, 'getProvinsi'])->name('api.provinsi');
        Route::get('/api/kabupaten', [CustomerSearchController::class, 'getKabupaten'])->name('api.kabupaten');
        Route::get('/api/statistics', [CustomerSearchController::class, 'getStatistics'])->name('api.statistics');

        // Advanced search routes
        Route::get('/search/fat', [CustomerSearchController::class, 'searchByFAT'])->name('search.fat');
        Route::get('/search/advanced', [CustomerSearchController::class, 'advancedSearch'])->name('search.advanced');
        Route::post('/search/advanced', [CustomerSearchController::class, 'advancedSearch'])->name('search.advanced.post');
        Route::get('/statistics', [CustomerSearchController::class, 'getStatistics'])->name('statistics');
        Route::post('/export', [CustomerSearchController::class, 'exportSearch'])->name('export');
    });

    // ================== PROFILE ROUTES ================== //
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

    // ================== OTHER UTILITY ROUTES ================== //
    Route::get('/debug-images', [ReportController::class, 'debugImages'])->name('debug.images');
    Route::get('/debug-storage', [ReportActivityController::class, 'debugStorage']);
    Route::get('/fix-storage', [ReportActivityController::class, 'fixStorage']);

    // Debug route untuk user (hapus setelah selesai debug)
    Route::get('/test-user', function() {
        $user = Auth::user();
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_path' => $user->profile_photo_path,
            'file_exists_storage' => $user->profile_photo_path ? file_exists(storage_path('app/public/' . $user->profile_photo_path)) : false,
            'storage_url' => $user->profile_photo_path ? Storage::url($user->profile_photo_path) : null,
        ];
    });
});

// ================== FALLBACK ROUTE ================== //
Route::fallback(function () {
    return redirect()->route('login');
});




Route::prefix('export')->group(function () {
    // Activity
    Route::get('/activity', [ExportController::class, 'activityView'])->name('export.activity');
    Route::get('/activity/pdf', [ExportController::class, 'exportActivityPdf'])->name('export.activity.pdf');
    Route::get('/activity/csv', [ExportController::class, 'exportActivityCsv'])->name('export.activity.csv');
    Route::get('/export/activity/excel', [ExportActivityController::class, 'exportExcel'])->name('export.activity.excel');

    //Competitor
    Route::get('/export/competitor', [ExportCompetitorController::class, 'index'])->name('export.competitor');
    Route::get('/export/competitor/pdf', [ExportCompetitorController::class, 'exportPdf'])->name('export.competitor.pdf');
    Route::get('/export/competitor/csv', [ExportCompetitorController::class, 'exportCsv'])->name('export.competitor.csv');
    Route::get('/export/competitor/excel', [ExportCompetitorController::class, 'exportExcel'])->name('export.competitor.excel');

    // Operational
    Route::get('/operational', [ExportController::class, 'operationalView'])->name('export.operational');
    Route::get('/operational/pdf', [ExportController::class, 'exportOperationalPdf'])->name('export.operational.pdf');
    Route::get('/operational/csv', [ExportController::class, 'exportOperationalCsv'])->name('export.operational.csv');
});


// ================== FALLBACK ROUTE ================== //
Route::fallback(function () {
    return redirect()->route('login');
});

Route::prefix('customer')->group(function () {
    Route::get('/search', [CustomerSearchController::class, 'index'])->name('customer.search');
    Route::get('/search/advanced', [CustomerSearchController::class, 'advancedSearch'])->name('customer.search.advanced');
    Route::get('customer/search/{id}/edit', [CustomerSearchController::class, 'edit'])->name('customer.search.edit');
    Route::put('/search/{id}', [CustomerSearchController::class, 'update'])->name('customer.update');
    Route::delete('/search/{id}', [CustomerSearchController::class, 'destroy'])->name('customer.delete');
    Route::get('/map', [CustomerSearchController::class, 'showMap'])->name('customer.map');
    Route::delete('/customer/{id}', [CustomerSearchController::class, 'destroy'])->name('customer.destroy');
    Route::delete('/customer/{id}', [CustomerSearchController::class, 'destroy'])
        ->name('customer.destroy');
    Route::delete('/customer/{id}', [CustomerSearchController::class, 'destroy'])->name('customer.destroy');
    Route::delete('/report/operational/{id}', [CustomerSearchController::class, 'destroy'])
    ->name('pelanggan.destroy');

    Route::delete('/pelanggan/{pelanggan}', [CustomerSearchController::class, 'destroy'])->name('pelanggan.destroy');

});

// Route::prefix('report')->middleware('auth')->group(function () {

//     // Operational Report Routes
//     Route::get('/operational', [OperationalReportController::class, 'index'])
//         ->name('report.operational.index');

//     Route::post('/operational', [OperationalReportController::class, 'store'])
//         ->name('report.operational.store');

//     Route::get('/operational/{id}/edit', [OperationalReportController::class, 'edit'])
//         ->name('report.operational.edit');

//     Route::put('/operational/{id}', [OperationalReportController::class, 'update'])
//         ->name('report.operational.update');

//     Route::delete('/operational/{id}', [OperationalReportController::class, 'destroy'])
//         ->name('report.operational.destroy');

//     Route::get('/operational/export', [OperationalReportController::class, 'export'])
//         ->name('report.operational.export');

//     // Customer Search Routes
//     Route::get('/customer/search', [CustomerSearchController::class, 'index'])
//         ->name('customer.search');

//     Route::get('/customer/{id}/edit', [CustomerSearchController::class, 'edit'])
//         ->name('customer.edit');

//     Route::put('/customer/{id}', [CustomerSearchController::class, 'update'])
//         ->name('customer.update');

//     Route::delete('/customer/{id}', [CustomerSearchController::class, 'destroy'])
//         ->name('customer.destroy');

//     Route::get('/customer/map', [CustomerSearchController::class, 'showMap'])
//         ->name('customer.map');
// });

// Route untuk halaman operational
// Route untuk halaman operational
Route::get('/report/operational', [OperationalReportController::class, 'index'])->name('report.operational.index');
Route::post('/report/operational/store', [OperationalReportController::class, 'store'])->name('report.operational.store');

// Route untuk Edit & Delete
Route::get('/report/operational/edit/{id}', [OperationalReportController::class, 'edit'])->name('report.operational.edit');
Route::put('/report/operational/update/{id}', [OperationalReportController::class, 'update'])->name('report.operational.update');
Route::delete('/report/operational/destroy/{id}', [OperationalReportController::class, 'destroy'])->name('report.operational.destroy');


// Route untuk AJAX (dropdown kabupaten dan generate FAT code)
Route::get('/report/operational/get-kabupaten', [OperationalReportController::class, 'getKabupaten'])->name('report.operational.get-kabupaten');
Route::get('/report/operational/get-kode-fat', [OperationalReportController::class, 'getKodeFat'])->name('report.operational.get-kode-fat');










Route::get('/get-kabupaten', [App\Http\Controllers\OperationalReportController::class, 'getKabupaten']);
Route::get('/get-kode-fat', [App\Http\Controllers\OperationalReportController::class, 'getKodeFat']);
Route::get('/api/kecepatan-by-bandwidth', [OperationalReportController::class, 'getKecepatanByBandwidth']);
Route::get('/get-kecepatan/{cluster}', [App\Http\Controllers\CompetitorController::class, 'getKecepatan']);
Route::get('/get-kecepatan-by-bandwidth', [App\Http\Controllers\CompetitorController::class, 'getKecepatanByBandwidth']);
Route::get('/get-kecepatan', [OperationalReportController::class, 'getKecepatanByBandwidth']);
Route::get('/get-kecepatan', [App\Http\Controllers\CompetitorController::class, 'getKecepatanByBandwidth'])->name('get.kecepatan');
Route::get('/operational-report', [OperationalReportController::class, 'index'])->name('operational.index');
Route::get('/get-kecepatan', [OperationalReportController::class, 'getKecepatan'])->name('get.kecepatan');


// Export routes group dengan middleware auth
Route::middleware(['auth'])->group(function () {

    // Export main page
    Route::get('/export', [ExportController::class, 'index'])
        ->name('export.index');

    // Export preview with filters
    Route::get('/export/preview', [ExportController::class, 'preview'])
        ->name('export.preview');

    // Export to CSV
    Route::get('/export/csv', [ExportController::class, 'exportCsv'])
        ->name('export.csv');

    // Export to PDF
    Route::get('/export/pdf', [ExportController::class, 'exportPdf'])
        ->name('export.pdf');

    // Get available roles for filter dropdown
    Route::get('/export/roles', [ExportController::class, 'getRoles'])
        ->name('export.roles');

});

// Jika Anda memerlukan routes tambahan untuk admin, uncomment di bawah ini:
/*
Route::middleware(['auth', 'admin'])->prefix('admin/export')->name('admin.export.')->group(function () {
    // Custom admin export routes
});
*/
