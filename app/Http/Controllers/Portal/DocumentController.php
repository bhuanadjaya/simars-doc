<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

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
        // F08 — full implementation coming next
        abort_unless($document->status === 'active', 404);
        $document->load(['documentType', 'ownerUnit', 'uploader', 'files', 'parentDocument', 'replacedBy', 'replaces']);
        return view('portal.documents.show', compact('document'));
    }

    public function download(Document $document): RedirectResponse|Response
    {
        // F08 — full implementation coming next
        abort_unless($document->status === 'active', 404);
        return back()->with('error', 'Fitur unduh akan segera tersedia.');
    }

    public function stream(Document $document): Response
    {
        // F08 — full implementation coming next
        abort_unless($document->status === 'active', 404);
        abort(501, 'Fitur stream akan segera tersedia.');
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
