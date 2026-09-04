<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\System\SystemController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\ExternalRegulationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Portal\DocumentController as PortalDocumentController;
use Illuminate\Support\Facades\Route;

// Landing page (public)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ── Auth ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// ── Notifications (auth, any role) ────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

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

        // Harus sebelum index/show agar tidak ditangkap wildcard {document}
        Route::get('documents/search-parents', [AdminDocumentController::class, 'searchParents'])
            ->name('documents.search-parents');

        Route::get('documents/export-excel', [AdminDocumentController::class, 'exportExcel'])
            ->name('documents.export-excel');

        Route::resource('documents', AdminDocumentController::class)->only(['index', 'show']);

        Route::patch('documents/{document}/publish', [AdminDocumentController::class, 'publish'])
            ->name('documents.publish')
            ->middleware('role:super_admin,admin_unit');

        Route::patch('documents/{document}/obsolete', [AdminDocumentController::class, 'obsolete'])
            ->name('documents.obsolete')
            ->middleware('role:super_admin,admin_unit');

        Route::patch('documents/{document}/revert-to-draft', [AdminDocumentController::class, 'revertToDraft'])
            ->name('documents.revert-to-draft')
            ->middleware('role:super_admin');

        Route::post('documents/{document}/review', [AdminDocumentController::class, 'review'])
            ->name('documents.review')
            ->middleware('role:super_admin,admin_unit');

        Route::post('documents/{document}/unreview', [AdminDocumentController::class, 'unreview'])
            ->name('documents.unreview')
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

        // Document Type Management (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('document-types', DocumentTypeController::class)
                ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
            Route::post('document-types/{documentType}/deactivate', [DocumentTypeController::class, 'deactivate'])
                ->name('document-types.deactivate');
            Route::post('document-types/{documentType}/activate', [DocumentTypeController::class, 'activate'])
                ->name('document-types.activate');
        });

        // F10 — User & Unit Management (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
            Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
            Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

            Route::resource('units', UnitController::class)->only(['index', 'create', 'store', 'edit', 'update']);
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

// ── System admin area ─────────────────────────────────────────────────
Route::prefix('system')->name('system.')->middleware(['auth', 'role:system_admin'])
    ->group(function () {
        Route::get('hospitals', [SystemController::class, 'hospitals'])->name('hospitals');
        Route::get('users', [SystemController::class, 'users'])->name('users');
        Route::get('users/{user}/edit', [SystemController::class, 'editUser'])->name('users.edit');
        Route::put('users/{user}', [SystemController::class, 'updateUser'])->name('users.update');
        Route::get('hospitals/{hospital}/units', [SystemController::class, 'unitsByHospital'])->name('hospitals.units');
        Route::get('activity-log', [SystemController::class, 'activityLog'])->name('activity-log');
    });

// ── User portal ───────────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->middleware(['auth'])
    ->group(function () {
        Route::get('documents', [PortalDocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/export-excel', [PortalDocumentController::class, 'exportExcel'])->name('documents.export-excel');
        Route::get('documents/{document}', [PortalDocumentController::class, 'show'])->name('documents.show');
        Route::get('documents/{document}/download', [PortalDocumentController::class, 'download'])->name('documents.download');
        Route::get('documents/{document}/stream', [PortalDocumentController::class, 'stream'])->name('documents.stream');
    });
