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

/*
|--------------------------------------------------------------------------
| Web Routes - CLEANED & ORGANIZED
|--------------------------------------------------------------------------
*/

// ================== ROOT REDIRECT ================== //
Route::get('/', function () {
    return redirect()->route('login');
});

// ================== AUTH ROUTES (PUBLIC) ================== //
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot.password');
    Route::post('/send-reset-code', [AuthController::class, 'sendResetCode'])->name('send.reset.code');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('reset.password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password.post');
});

// Logout (must be authenticated)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

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
    Route::resource('users', UserController::class);

    // ================== PROFILE ROUTES ================== //
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'changePasswordForm'])->name('change.password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update.password');
        Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('photo.delete');
    });

    // ================== REPORT ROUTES ================== //
    Route::prefix('reports')->name('reports.')->group(function () {
        // Report Activity CRUD
        Route::get('/activity', [ReportActivityController::class, 'index'])->name('activity');
        Route::post('/activity', [ReportActivityController::class, 'store'])->name('store');
        Route::get('/activity/{id}/edit', [ReportActivityController::class, 'edit'])->name('edit');
        Route::put('/activity/{id}', [ReportActivityController::class, 'update'])->name('update');
        Route::delete('/activity/{id}', [ReportActivityController::class, 'destroy'])->name('destroy');

        // Export Activity
        Route::get('/activity/export', [ReportActivityController::class, 'export'])->name('export');
        Route::get('/activity/export-pdf', [ReportActivityController::class, 'exportPdf'])->name('exportPdf');
        Route::get('/activity/export-csv', [ReportActivityController::class, 'exportCsv'])->name('exportCsv');
        Route::get('/activity/print', [ReportActivityController::class, 'printView'])->name('print');

        // Report Competitor (view only)
        Route::get('/competitor', [ReportController::class, 'competitor'])->name('competitor');

        // Debug routes (remove after production)
        Route::get('/debug', [ReportController::class, 'debugData'])->name('debug');
        Route::get('/refresh', [ReportController::class, 'refresh'])->name('refresh');
    });

    // ================== COMPETITOR ROUTES ================== //
    Route::resource('competitor', CompetitorController::class);

    // API untuk dropdown competitor
    Route::get('/get-kecepatan/{cluster}', [CompetitorController::class, 'getKecepatan'])->name('competitor.get.kecepatan');
    Route::get('/get-kecepatan-by-bandwidth', [CompetitorController::class, 'getKecepatanByBandwidth'])->name('competitor.get.kecepatan.bandwidth');

    // ================== OPERATIONAL REPORT ROUTES ================== //
    Route::prefix('report/operational')->name('report.operational.')->group(function () {
        // Main CRUD
        Route::get('/', [OperationalReportController::class, 'index'])->name('index');
        Route::post('/', [OperationalReportController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [OperationalReportController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OperationalReportController::class, 'update'])->name('update');
        Route::delete('/{id}', [OperationalReportController::class, 'destroy'])->name('destroy');

        // View & Export
        Route::get('/show', [OperationalReportController::class, 'show'])->name('show');
        Route::get('/export', [OperationalReportController::class, 'export'])->name('export');

        // API Endpoints untuk dropdown dinamis
        Route::get('/get-kabupaten', [OperationalReportController::class, 'getKabupaten'])->name('get-kabupaten');
        Route::get('/get-kode-fat', [OperationalReportController::class, 'getKodeFat'])->name('get-kode-fat');
        Route::get('/get-kecepatan', [OperationalReportController::class, 'getKecepatanByBandwidth'])->name('get-kecepatan');
    });

    // ================== CUSTOMER SEARCH ROUTES ================== //
    Route::prefix('customer')->name('customer.')->group(function () {
        // Main search
        Route::get('/search', [CustomerSearchController::class, 'index'])->name('search');

        // CRUD operations
        Route::get('/{id}/edit', [CustomerSearchController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerSearchController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerSearchController::class, 'destroy'])->name('destroy');

        // Map view
        Route::get('/map', [CustomerSearchController::class, 'showMap'])->name('map');

        // Detail view (AJAX)
        Route::get('/{id}/detail', [CustomerSearchController::class, 'getDetail'])->name('detail');

        // Advanced search
        Route::get('/search/advanced', [CustomerSearchController::class, 'advancedSearch'])->name('search.advanced');
        Route::post('/search/advanced', [CustomerSearchController::class, 'advancedSearch'])->name('search.advanced.post');
        Route::get('/search/fat', [CustomerSearchController::class, 'searchByFAT'])->name('search.fat');

        // API Endpoints
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/provinsi', [CustomerSearchController::class, 'getProvinsi'])->name('provinsi');
            Route::get('/kabupaten', [CustomerSearchController::class, 'getKabupaten'])->name('kabupaten');
            Route::get('/statistics', [CustomerSearchController::class, 'getStatistics'])->name('statistics');
        });

        // Export
        Route::post('/export', [CustomerSearchController::class, 'exportSearch'])->name('export');
    });

    // Alternative customer routes (for compatibility with old links)
    Route::get('/report/customer/search', [CustomerSearchController::class, 'index'])->name('report.customer.search');
    Route::get('/report/customer/{id}/edit', [CustomerSearchController::class, 'edit'])->name('report.customer.edit');
    Route::put('/report/customer/{id}', [CustomerSearchController::class, 'update'])->name('report.customer.update');
    Route::delete('/report/customer/{id}', [CustomerSearchController::class, 'destroy'])->name('report.customer.destroy');
    Route::get('/report/customer/map', [CustomerSearchController::class, 'showMap'])->name('report.customer.map');

    // ================== EXPORT ROUTES ================== //
    Route::prefix('export')->name('export.')->group(function () {
        // Main export page
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::get('/preview', [ExportController::class, 'preview'])->name('preview');
        Route::get('/roles', [ExportController::class, 'getRoles'])->name('roles');

        // Activity Export
        Route::prefix('activity')->name('activity.')->group(function () {
            Route::get('/', [ExportActivityController::class, 'index'])->name('index');
            Route::get('/pdf', [ExportActivityController::class, 'exportPdf'])->name('pdf');
            Route::get('/csv', [ExportActivityController::class, 'exportCsv'])->name('csv');
            Route::get('/excel', [ExportActivityController::class, 'exportExcel'])->name('excel');
        });

        // Competitor Export
        Route::prefix('competitor')->name('competitor.')->group(function () {
            Route::get('/', [ExportCompetitorController::class, 'index'])->name('index');
            Route::get('/pdf', [ExportCompetitorController::class, 'exportPdf'])->name('pdf');
            Route::get('/csv', [ExportCompetitorController::class, 'exportCsv'])->name('csv');
            Route::get('/excel', [ExportCompetitorController::class, 'exportExcel'])->name('excel');
        });

        // Operational Export
        Route::prefix('operational')->name('operational.')->group(function () {
            Route::get('/', [ExportController::class, 'operationalView'])->name('index');
            Route::get('/pdf', [ExportController::class, 'exportOperationalPdf'])->name('pdf');
            Route::get('/csv', [ExportController::class, 'exportOperationalCsv'])->name('csv');
        });
    });

    // ================== UTILITY & DEBUG ROUTES ================== //
    // Debug routes (remove in production)
    Route::prefix('debug')->name('debug.')->group(function () {
        Route::get('/images', [ReportController::class, 'debugImages'])->name('images');
        Route::get('/storage', [ReportActivityController::class, 'debugStorage'])->name('storage');
        Route::get('/fix-storage', [ReportActivityController::class, 'fixStorage'])->name('fix.storage');

        // Test user profile
        Route::get('/test-user', function() {
            $user = auth()->user();
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_path' => $user->profile_photo_path,
                'file_exists_storage' => $user->profile_photo_path ? file_exists(storage_path('app/public/' . $user->profile_photo_path)) : false,
                'storage_url' => $user->profile_photo_path ? \Storage::url($user->profile_photo_path) : null,
            ];
        })->name('test.user');
    });
});

// ================== FALLBACK ROUTE ================== //
Route::fallback(function () {
    if (auth()->check()) {
        return redirect()->route('dashboard')->with('error', 'Halaman tidak ditemukan');
    }
    return redirect()->route('login')->with('error', 'Halaman tidak ditemukan');
});
