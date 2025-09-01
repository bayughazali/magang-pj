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