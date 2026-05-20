<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Unit;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::active()->with(['documentType', 'ownerUnit']);

        // Full-text search (3+ chars) or fallback LIKE
        if ($request->filled('q')) {
            $q = $request->q;
            if (mb_strlen($q) >= 3) {
                try {
                    $query->whereFullText(['title', 'description', 'tags'], $q);
                } catch (\Throwable) {
                    $this->applyLikeSearch($query, $q);
                }
            } else {
                $this->applyLikeSearch($query, $q);
            }
        }

        if ($request->filled('type')) {
            $query->where('document_type_id', $request->type);
        }

        if ($request->filled('unit')) {
            $query->where('owner_unit_id', $request->unit);
        }

        if ($request->filled('year')) {
            $query->whereYear('effective_date', $request->year);
        }

        match ($request->get('sort', 'latest')) {
            'title_asc' => $query->orderBy('title'),
            'type'      => $query->orderBy('document_type_id')->orderBy('title'),
            default     => $query->orderByDesc('effective_date')->orderByDesc('created_at'),
        };

        $documents     = $query->paginate(20)->withQueryString();
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $units         = Unit::where('is_active', true)->orderBy('name')->get();
        $years         = Document::active()
            ->whereNotNull('effective_date')
            ->selectRaw('YEAR(effective_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('portal.documents.index', compact('documents', 'documentTypes', 'units', 'years'));
    }

    public function show(Document $document): View
    {
        abort_unless($document->status === 'active', 404);
        $document->load(['documentType', 'ownerUnit', 'uploader', 'files', 'parentDocument', 'replacedBy', 'replaces']);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $canDownload = $user->role->name === 'super_admin'
            || ($user->role->name === 'admin_unit' && $document->owner_unit_id === $user->unit_id);

        app(ActivityLogService::class)->log($user, 'view_document', $document);

        return view('portal.documents.show', compact('document', 'canDownload'));
    }

    public function download(Document $document): RedirectResponse|Response|StreamedResponse
    {
        abort_unless($document->status === 'active', 404);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $canDownload = $user->role->name === 'super_admin'
            || ($user->role->name === 'admin_unit' && $document->owner_unit_id === $user->unit_id);

        abort_unless($canDownload, 403);

        $pdfFile = $document->pdfFile;
        abort_unless($pdfFile && \Illuminate\Support\Facades\Storage::disk('local')->exists($pdfFile->file_path), 404);

        app(ActivityLogService::class)->log($user, 'download_document', $document);

        return \Illuminate\Support\Facades\Storage::disk('local')->download(
            $pdfFile->file_path,
            $pdfFile->original_filename
        );
    }

    public function stream(Document $document): Response
    {
        abort_unless($document->status === 'active', 404);

        $pdfFile = $document->pdfFile;
        abort_unless($pdfFile && \Illuminate\Support\Facades\Storage::disk('local')->exists($pdfFile->file_path), 404);

        $contents = \Illuminate\Support\Facades\Storage::disk('local')->get($pdfFile->file_path);

        return response($contents, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdfFile->original_filename . '"',
        ]);
    }

    private function applyLikeSearch($query, string $q): void
    {
        $query->where(function ($sub) use ($q) {
            $sub->where('title', 'like', "%{$q}%")
                ->orWhere('number', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->orWhere('tags', 'like', "%{$q}%");
        });
    }
}
