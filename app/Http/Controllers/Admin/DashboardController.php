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
        $totalObsolete  = Document::obsolete()->count();
        $totalReviewed  = Document::where('is_reviewed', true)->count();

        $totalExpiringSoon = Document::whereNotNull('expired_at')
            ->whereNotNull('reminder_months')
            ->whereRaw('expired_at >= CURDATE()')
            ->whereRaw('CURDATE() >= DATE_SUB(expired_at, INTERVAL reminder_months MONTH)')
            ->count();

        $totalOverdue = Document::whereNotNull('expired_at')
            ->whereRaw('expired_at < CURDATE()')
            ->count();

        $recentActivity = ActivityLog::with(['user', 'document'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalActive', 'newThisMonth', 'totalObsolete',
            'totalReviewed', 'totalExpiringSoon', 'totalOverdue',
            'recentActivity'
        ));
    }
}
