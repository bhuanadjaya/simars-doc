<?php

namespace App\Http\Controllers\Portal;

use App\Exports\DocumentsExport;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Unit;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        // Portal list: show restricted docs in list (withoutGlobalScope), but policy blocks detail access
        $query = Document::withoutGlobalScope('visibility')
            ->whereIn('status', ['active', 'obsolete'])
            ->with(['documentType', 'ownerUnit']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
        $years         = Document::withoutGlobalScope('visibility')
            ->whereIn('status', ['active', 'obsolete'])
            ->whereNotNull('effective_date')
            ->selectRaw('YEAR(effective_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        /** @var \App\Models\User $user */
        $user = auth()->user()->load('role');

        return view('portal.documents.index', compact('documents', 'documentTypes', 'units', 'years', 'user'));
    }

    public function show(Document $document): View|RedirectResponse
    {
        // Load from DB bypassing global scope (restricted docs appear in list)
        $document = Document::withoutGlobalScope('visibility')->findOrFail($document->id);

        if (! in_array($document->status, ['active', 'obsolete'])) {
            abort(404);
        }

        // Restricted check via policy
        /** @var \App\Models\User $user */
        $user = auth()->user()->load('role');
        if ($document->visibility === 'restricted' && $document->uploaded_by !== $user->id) {
            return view('portal.documents.restricted');
        }

        $document->load(['documentType', 'ownerUnit', 'uploader', 'files', 'parentDocument', 'replacedBy', 'replaces']);

        $canDownload = $user->role->name === 'super_admin'
            || ($user->role->name === 'admin_unit' && $document->owner_unit_id === $user->unit_id);

        app(ActivityLogService::class)->log($user, 'view_document', $document);

        return view('portal.documents.show', compact('document', 'canDownload'));
    }

    public function download(Document $document): RedirectResponse|Response|StreamedResponse
    {
        $document = Document::withoutGlobalScope('visibility')->findOrFail($document->id);

        if (! in_array($document->status, ['active', 'obsolete'])) {
            abort(404);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user()->load('role');

        if ($document->visibility === 'restricted' && $document->uploaded_by !== $user->id) {
            abort(403);
        }

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
        $document = Document::withoutGlobalScope('visibility')->findOrFail($document->id);

        if (! in_array($document->status, ['active', 'obsolete'])) {
            abort(404);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($document->visibility === 'restricted' && $document->uploaded_by !== $user->id) {
            abort(403);
        }

        $pdfFile = $document->pdfFile;
        abort_unless($pdfFile && \Illuminate\Support\Facades\Storage::disk('local')->exists($pdfFile->file_path), 404);

        $contents = \Illuminate\Support\Facades\Storage::disk('local')->get($pdfFile->file_path);

        $disposition = \Symfony\Component\HttpFoundation\HeaderUtils::makeDisposition(
            \Symfony\Component\HttpFoundation\HeaderUtils::DISPOSITION_INLINE,
            $pdfFile->original_filename,
            'document.pdf'
        );

        return response($contents, 200, [
            'Content-Type'           => 'application/pdf',
            'Content-Disposition'    => $disposition,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $query = Document::withoutGlobalScope('visibility')
            ->whereIn('status', ['active', 'obsolete'])
            ->with(['documentType', 'ownerUnit']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $this->applyLikeSearch($query, $request->q);
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

        $documents = $query->orderByDesc('created_at')->get();
        $filename  = 'daftar-dokumen-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new DocumentsExport($documents), $filename);
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
