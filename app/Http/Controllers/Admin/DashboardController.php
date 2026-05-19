<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Document;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalActive  = Document::active()->count();
        $newThisMonth = Document::active()
            ->whereYear('published_at', now()->year)
            ->whereMonth('published_at', now()->month)
            ->count();
        $totalObsolete = Document::obsolete()->count();

        $recentActivity = ActivityLog::with(['user', 'document'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('totalActive', 'newThisMonth', 'totalObsolete', 'recentActivity'));
    }
}
