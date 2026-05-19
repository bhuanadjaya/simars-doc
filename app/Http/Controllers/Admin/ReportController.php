<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function masterDocumentList(Request $request): View
    {
        $query = Document::active()->with(['documentType', 'ownerUnit', 'uploader']);

        if ($request->filled('type')) {
            $query->where('document_type_id', $request->type);
        }

        if ($request->filled('unit')) {
            $query->where('owner_unit_id', $request->unit);
        }

        if ($request->filled('year')) {
            $query->whereYear('effective_date', $request->year);
        }

        $documents     = $query->orderBy('number')->paginate(20)->withQueryString();
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $units         = Unit::where('is_active', true)->orderBy('name')->get();
        $years         = Document::active()
            ->whereNotNull('effective_date')
            ->selectRaw('YEAR(effective_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('admin.reports.master-document-list', compact('documents', 'documentTypes', 'units', 'years'));
    }

    public function exportExcel(Request $request): Response
    {
        $query = Document::active()->with(['documentType', 'ownerUnit', 'uploader']);

        if ($request->filled('type')) {
            $query->where('document_type_id', $request->type);
        }
        if ($request->filled('unit')) {
            $query->where('owner_unit_id', $request->unit);
        }
        if ($request->filled('year')) {
            $query->whereYear('effective_date', $request->year);
        }

        $documents = $query->orderBy('number')->get();

        $rows   = [];
        $rows[] = ['No', 'Nomor Dokumen', 'Judul', 'Jenis Dokumen', 'Unit Pemilik', 'Sumber', 'Tanggal Berlaku', 'Tanggal Publikasi', 'Diunggah Oleh'];

        foreach ($documents as $i => $doc) {
            $rows[] = [
                $i + 1,
                $doc->number,
                $doc->title,
                $doc->documentType?->name ?? '',
                $doc->ownerUnit?->name ?? '',
                $doc->source === 'internal' ? 'Internal' : 'Eksternal',
                $doc->effective_date?->format('d/m/Y') ?? '',
                $doc->published_at?->format('d/m/Y') ?? '',
                $doc->uploader?->name ?? '',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(';', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        $filename = 'daftar-induk-dokumen-' . now()->format('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request): View
    {
        $query = Document::active()->with(['documentType', 'ownerUnit', 'uploader']);

        if ($request->filled('type')) {
            $query->where('document_type_id', $request->type);
        }
        if ($request->filled('unit')) {
            $query->where('owner_unit_id', $request->unit);
        }
        if ($request->filled('year')) {
            $query->whereYear('effective_date', $request->year);
        }

        $documents = $query->orderBy('number')->get();

        return view('admin.reports.master-document-list-print', compact('documents'));
    }

    public function activityLog(Request $request): View
    {
        $query = ActivityLog::with(['user', 'document'])->latest();

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('document_id')) {
            $query->where('document_id', $request->document_id);
        }

        $logs    = $query->paginate(50)->withQueryString();
        $users   = User::orderBy('name')->get();
        $actions = ActivityLog::select('action')->distinct()->pluck('action')->sort()->values();

        return view('admin.reports.activity-log', compact('logs', 'users', 'actions'));
    }

    public function exportActivityLogExcel(Request $request): Response
    {
        $query = ActivityLog::with(['user', 'document'])->latest();

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(5000)->get();

        $rows   = [];
        $rows[] = ['Waktu', 'Pengguna', 'Aksi', 'Dokumen', 'IP Address'];

        foreach ($logs as $log) {
            $rows[] = [
                $log->created_at->format('d/m/Y H:i:s'),
                $log->user?->name ?? '—',
                $log->action,
                $log->document?->title ?? '—',
                $log->ip_address ?? '—',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(';', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        $filename = 'log-aktivitas-' . now()->format('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function usageStatistics(): View
    {
        $topDownloads = ActivityLog::where('action', 'download_document')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('document_id')
            ->selectRaw('COUNT(*) as download_count')
            ->groupBy('document_id')
            ->orderByDesc('download_count')
            ->limit(10)
            ->get()
            ->each(fn ($row) => $row->load('document'));

        $mostActiveUsers = ActivityLog::where('created_at', '>=', now()->subDays(30))
            ->select('user_id')
            ->selectRaw('COUNT(*) as activity_count')
            ->groupBy('user_id')
            ->orderByDesc('activity_count')
            ->limit(10)
            ->get()
            ->each(fn ($row) => $row->load('user.unit'));

        return view('admin.reports.usage-statistics', compact('topDownloads', 'mostActiveUsers'));
    }
}
