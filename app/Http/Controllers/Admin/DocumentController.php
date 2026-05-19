<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Unit;
use App\Services\ActivityLogService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentService $documentService,
        private ActivityLogService $activityLog,
    ) {}

    public function create(): View
    {
        $user          = auth()->user()->load('role', 'unit');
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $units         = Unit::where('is_active', true)->orderBy('name')->get();
        $documents     = Document::orderBy('title')->get(['id', 'number', 'title']);

        return view('admin.documents.create', compact('user', 'documentTypes', 'units', 'documents'));
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

    public function show(Document $document): View
    {
        $document->load(['documentType', 'ownerUnit', 'uploader', 'files', 'parentDocument', 'replacedBy']);

        return view('admin.documents.show', compact('document'));
    }
}
