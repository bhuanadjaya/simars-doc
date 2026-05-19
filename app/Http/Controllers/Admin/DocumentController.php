<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ObsoleteDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Unit;
use App\Services\ActivityLogService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentService $documentService,
        private ActivityLogService $activityLog,
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user()->load('role', 'unit');

        $base = Document::query();
        if ($user->role->name === 'admin_unit') {
            $base->where('owner_unit_id', $user->unit_id);
        }

        // Status counts for tab badges
        $counts = [
            'all'      => (clone $base)->count(),
            'draft'    => (clone $base)->where('status', 'draft')->count(),
            'active'   => (clone $base)->where('status', 'active')->count(),
            'obsolete' => (clone $base)->where('status', 'obsolete')->count(),
        ];

        // Apply filters
        $query = (clone $base)->with(['documentType', 'ownerUnit', 'uploader']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('document_type_id', $request->type);
        }
        if ($request->filled('unit') && $user->role->name !== 'admin_unit') {
            $query->where('owner_unit_id', $request->unit);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('number', 'like', "%{$q}%");
            });
        }

        $documents     = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $units         = $user->role->name !== 'admin_unit'
            ? Unit::where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('admin.documents.index', compact('user', 'documents', 'documentTypes', 'units', 'counts'));
    }

    public function create(): View
    {
        $user          = auth()->user()->load('role', 'unit');
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $units         = Unit::where('is_active', true)->orderBy('name')->get();

        return view('admin.documents.create', compact('user', 'documentTypes', 'units'));
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $user      = auth()->user()->load('role', 'unit');
        $validated = $request->validated();

        // admin_unit can only upload for their own unit
        if ($user->role->name === 'admin_unit') {
            $validated['owner_unit_id'] = $user->unit_id;
        }

        try {
            $document = $this->documentService->store(
                $validated,
                $request->file('pdf_file'),
                $request->file('docx_file'),
                $user,
            );
        } catch (Throwable) {
            return back()->withInput()
                ->with('error', 'Failed to upload document. Please try again.');
        }

        $this->activityLog->log($user, 'create_document', $document);

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Document "' . $document->title . '" uploaded successfully.');
    }

    public function edit(Document $document): View|RedirectResponse
    {
        if ($document->status !== 'draft') {
            return back()->with('error', 'Only draft documents can be edited.');
        }

        $this->authorize('update', $document);

        $document->load(['documentType', 'ownerUnit', 'files']);
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        return view('admin.documents.edit', compact('document', 'documentTypes'));
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        if ($document->status !== 'draft') {
            return back()->with('error', 'Only draft documents can be edited.');
        }

        $this->authorize('update', $document);

        $user      = auth()->user()->load('role', 'unit');
        $validated = $request->validated();

        // owner_unit_id is immutable — strip it from validated data if somehow submitted
        unset($validated['owner_unit_id']);

        try {
            $document = $this->documentService->update(
                $document,
                $validated,
                $request->file('pdf_file'),
                $request->file('docx_file'),
                $user,
            );
        } catch (Throwable) {
            return back()->withInput()
                ->with('error', 'Failed to update document. Please try again.');
        }

        $this->activityLog->log($user, 'edit_document', $document);

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Document "' . $document->title . '" updated successfully.');
    }

    public function publish(Document $document): RedirectResponse
    {
        if ($document->status !== 'draft') {
            return back()->with('error', 'Hanya dokumen berstatus Draft yang dapat dipublikasikan.');
        }

        $this->authorize('publish', $document);

        $user = auth()->user()->load('role');

        try {
            $this->documentService->publish($document);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->activityLog->log($user, 'publish_document', $document);

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Dokumen "' . $document->title . '" berhasil dipublikasikan.');
    }

    public function obsolete(ObsoleteDocumentRequest $request, Document $document): RedirectResponse
    {
        if ($document->status !== 'active') {
            return back()->with('error', 'Hanya dokumen aktif yang dapat dinyatakan obsolet.');
        }

        $this->authorize('obsolete', $document);

        $user = auth()->user()->load('role');

        try {
            $this->documentService->setObsolete(
                $document,
                $user,
                $request->validated('obsolete_reason'),
                $request->validated('replaced_by_id'),
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->activityLog->log($user, 'set_obsolete', $document);

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Dokumen "' . $document->title . '" telah dinyatakan obsolet.');
    }

    public function show(Document $document): View
    {
        $document->load(['documentType', 'ownerUnit', 'uploader', 'files', 'parentDocument', 'replacedBy']);

        // Active documents on show page: load active docs for the replaced_by_id modal select
        $activeDocuments = $document->status === 'active'
            ? Document::active()->where('id', '!=', $document->id)->orderBy('title')->get(['id', 'number', 'title'])
            : collect();

        return view('admin.documents.show', compact('document', 'activeDocuments'));
    }
}
