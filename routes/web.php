<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\ExternalRegulationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Portal\DocumentController as PortalDocumentController;
use Illuminate\Support\Facades\Route;

// Root → redirect to login
Route::get('/', fn () => redirect('/login'));

// ── Auth ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// ── Notifications (auth, any role) ────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/list', [NotificationController::class, 'list'])->name('notifications.list');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

// ── Admin area ────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin,admin_unit,auditor'])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // F02–F06 — Documents
        // create/edit harus didaftarkan sebelum show agar /create tidak tertangkap {document} wildcard
        Route::resource('documents', AdminDocumentController::class)->only(['create', 'store', 'edit', 'update'])
            ->middleware('role:super_admin,admin_unit');

        Route::resource('documents', AdminDocumentController::class)->only(['index', 'show']);

        Route::patch('documents/{document}/publish', [AdminDocumentController::class, 'publish'])
            ->name('documents.publish')
            ->middleware('role:super_admin,admin_unit');

        Route::patch('documents/{document}/obsolete', [AdminDocumentController::class, 'obsolete'])
            ->name('documents.obsolete')
            ->middleware('role:super_admin,admin_unit');

        Route::delete('documents/{document}', [AdminDocumentController::class, 'destroy'])
            ->name('documents.destroy')
            ->middleware('role:super_admin,admin_unit');

        // PDF stream for admin preview modal (all admin roles)
        Route::get('documents/{document}/stream', [AdminDocumentController::class, 'stream'])
            ->name('documents.stream');

        // File download for admin (all admin roles)
        Route::get('documents/{document}/download', [AdminDocumentController::class, 'download'])
            ->name('documents.download');

        // F09 — External Regulation Management (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('external-regulations', ExternalRegulationController::class);
            Route::get('external-regulations/{externalRegulation}/download', [ExternalRegulationController::class, 'download'])
                ->name('external-regulations.download');
            Route::get('external-regulations/{externalRegulation}/stream', [ExternalRegulationController::class, 'stream'])
                ->name('external-regulations.stream');
        });

        // F10 — User & Unit Management (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
            Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
            Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

            Route::resource('units', UnitController::class)->only(['index', 'create', 'store']);
            Route::post('units/{unit}/deactivate', [UnitController::class, 'deactivate'])->name('units.deactivate');
            Route::post('units/{unit}/activate', [UnitController::class, 'activate'])->name('units.activate');
        });

        // F11 — Reports (super_admin + auditor)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('master-document-list', [ReportController::class, 'masterDocumentList'])->name('master-document-list');
            Route::get('export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
            Route::get('export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
            Route::get('activity-log', [ReportController::class, 'activityLog'])->name('activity-log');
            Route::get('export-activity-log', [ReportController::class, 'exportActivityLogExcel'])->name('export-activity-log');
            Route::get('usage-statistics', [ReportController::class, 'usageStatistics'])->name('usage-statistics');
        });
    });

// ── User portal ───────────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->middleware(['auth'])
    ->group(function () {
        Route::get('documents', [PortalDocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/{document}', [PortalDocumentController::class, 'show'])->name('documents.show');
        Route::get('documents/{document}/download', [PortalDocumentController::class, 'download'])->name('documents.download');
        Route::get('documents/{document}/stream', [PortalDocumentController::class, 'stream'])->name('documents.stream');
    });
