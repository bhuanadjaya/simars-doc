<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
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

// ── Admin area ────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin,admin_unit,auditor'])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Read-only: all admin roles (including auditor)
        Route::resource('documents', AdminDocumentController::class)->only(['index', 'show']);

        // Write operations: super_admin + admin_unit only
        Route::resource('documents', AdminDocumentController::class)->only(['create', 'store', 'edit', 'update'])
            ->middleware('role:super_admin,admin_unit');

        // F04 — Publish document
        Route::patch('documents/{document}/publish', [AdminDocumentController::class, 'publish'])
            ->name('documents.publish')
            ->middleware('role:super_admin,admin_unit');

        // F05 — Set obsolete
        Route::patch('documents/{document}/obsolete', [AdminDocumentController::class, 'obsolete'])
            ->name('documents.obsolete')
            ->middleware('role:super_admin,admin_unit');

        // Future features (F06, F10–F12, F14) will be added here
    });

// ── User portal ───────────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->middleware(['auth'])
    ->group(function () {
        Route::get('documents', [PortalDocumentController::class, 'index'])->name('documents.index');

        // Future features (F07–F09, F13) will be added here
    });
