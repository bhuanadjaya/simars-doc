<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function index(Request $request): View
    {
        $query = DocumentType::withCount('documents');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $types = $query->orderBy('code')->paginate(20)->withQueryString();

        return view('admin.document-types.index', compact('types'));
    }

    public function create(): View
    {
        return view('admin.document-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:document_types,code'],
            'name' => ['required', 'string', 'max:100'],
        ]);

        DocumentType::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Jenis dokumen "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    public function edit(DocumentType $documentType): View
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('document_types', 'code')->ignore($documentType->id)],
            'name' => ['required', 'string', 'max:100'],
        ]);

        $documentType->update($validated);

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Jenis dokumen "' . $documentType->name . '" berhasil diperbarui.');
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        if ($documentType->documents()->exists()) {
            return redirect()->route('admin.document-types.index')
                ->with('error', 'Jenis dokumen tidak dapat dihapus karena masih memiliki dokumen terkait.');
        }

        $name = $documentType->name;
        $documentType->delete();

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Jenis dokumen "' . $name . '" berhasil dihapus.');
    }

    public function deactivate(DocumentType $documentType): RedirectResponse
    {
        if ($documentType->documents()->where('status', 'active')->exists()) {
            return redirect()->route('admin.document-types.index')
                ->with('error', 'Jenis dokumen tidak dapat dinonaktifkan karena masih memiliki dokumen aktif.');
        }

        $documentType->update(['is_active' => false]);

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Jenis dokumen "' . $documentType->name . '" berhasil dinonaktifkan.');
    }

    public function activate(DocumentType $documentType): RedirectResponse
    {
        $documentType->update(['is_active' => true]);

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Jenis dokumen "' . $documentType->name . '" berhasil diaktifkan kembali.');
    }
}
