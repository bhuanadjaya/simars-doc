# SIMARS-DOC — Tech Stack & Project Conventions
> **For Claude Code:** Read this file before writing any code.
> All technical decisions are defined here. Do not use packages or patterns
> not listed here without confirmation.

---

## Main Stack

| Layer | Choice | Version |
|-------|--------|---------|
| Backend framework | Laravel | 12.59.0 |
| Auth starter | Laravel Breeze | — |
| Frontend | Blade + jQuery | jQuery 3.x via CDN |
| Database | MySQL | 8.x |
| File storage | Local disk (storage/app) | — |
| CSS | Tailwind CSS | 3.x (included in Breeze) |
| Queue | Laravel Queue (database driver) | — |
| PDF | barryvdh/laravel-dompdf | watermark & export |
| Excel export | maatwebsite/excel | report export |

**Not used:** Livewire, Inertia, Vue, React, Filament, Alpine.js.

---

## Two Main Areas

```
1. ADMIN AREA  → /admin
   Access: super_admin, admin_unit
   Purpose: upload documents, manage users/units, set obsolete, reports

2. USER PORTAL → /portal
   Access: all roles including user and guest
   Purpose: search & read documents, download, notifications
```

**Pattern:** All requests → Controller → return view() with data.
Light interactions (modal confirm, show/hide) use jQuery inline or in a
dedicated `public/js/app.js` file.

---

## Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── DocumentController.php
│   │   │   ├── UserController.php
│   │   │   ├── UnitController.php
│   │   │   ├── DocumentTypeController.php
│   │   │   ├── ExternalRegulationController.php
│   │   │   ├── ReportController.php
│   │   │   └── ArchiveController.php
│   │   └── Portal/
│   │       ├── DocumentController.php
│   │       └── ExternalRegulationController.php
│   ├── Middleware/
│   │   └── CheckRole.php
│   └── Requests/
│       ├── StoreDocumentRequest.php
│       ├── UpdateDocumentRequest.php
│       ├── ObsoleteDocumentRequest.php
│       └── StoreExternalRegulationRequest.php
├── Models/
│   ├── Document.php
│   ├── DocumentFile.php
│   ├── DocumentType.php
│   ├── ActivityLog.php
│   ├── Notification.php
│   ├── ExternalRegulation.php
│   ├── ObsoleteRetention.php
│   ├── Role.php
│   ├── Unit.php
│   └── User.php
├── Policies/
│   ├── DocumentPolicy.php
│   └── ExternalRegulationPolicy.php
└── Services/
    ├── DocumentService.php
    ├── WatermarkService.php
    └── ActivityLogService.php

resources/
└── views/
    ├── layouts/
    │   ├── admin.blade.php         ← Admin sidebar layout
    │   └── portal.blade.php        ← Portal navbar layout
    ├── components/
    │   ├── alert.blade.php         ← Flash message success/error
    │   ├── status-badge.blade.php  ← draft / active / obsolete badge
    │   └── confirm-modal.blade.php ← jQuery modal for destructive actions
    ├── admin/
    │   ├── dashboard.blade.php
    │   ├── documents/
    │   │   ├── index.blade.php     ← Document list + filter
    │   │   ├── create.blade.php    ← Upload new document form
    │   │   ├── edit.blade.php      ← Edit draft document form
    │   │   └── show.blade.php      ← Detail + action buttons
    │   ├── users/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   └── edit.blade.php
    │   ├── units/
    │   │   ├── index.blade.php
    │   │   └── create.blade.php
    │   ├── document-types/
    │   │   ├── index.blade.php
    │   │   └── create.blade.php
    │   ├── external-regulations/
    │   │   ├── index.blade.php
    │   │   └── create.blade.php
    │   ├── reports/
    │   │   └── index.blade.php
    │   └── archive/
    │       └── index.blade.php
    └── portal/
        ├── documents/
        │   ├── index.blade.php     ← Search + browse
        │   └── show.blade.php      ← Detail & inline PDF viewer
        └── external-regulations/
            └── index.blade.php

public/
└── js/
    └── app.js                      ← jQuery scripts (modal, confirm, etc.)
```

---

## Naming Conventions

| Thing | Convention | Example |
|-------|-----------|---------|
| Table | snake_case, plural, English | `documents`, `document_types`, `activity_logs` |
| Column | snake_case, English | `owner_unit_id`, `effective_date`, `uploaded_by` |
| Model | PascalCase, singular | `Document`, `DocumentType`, `ActivityLog` |
| Controller | PascalCase + Controller | `DocumentController`, `UnitController` |
| Form Request | Store/Update + Model + Request | `StoreDocumentRequest` |
| Service | PascalCase + Service | `DocumentService`, `WatermarkService` |
| Policy | PascalCase + Policy | `DocumentPolicy` |
| Route name | snake_case dot notation | `admin.documents.show`, `portal.documents.index` |
| View folder | kebab-case, plural | `admin/documents/`, `admin/document-types/` |
| Blade file | snake_case | `create.blade.php`, `show.blade.php` |
| JS function | camelCase | `openObsoleteModal()`, `confirmDelete()` |

---
## Existing Template

The project already has a Blade layout template. Claude Code must extend
this existing template — do NOT create a new layout from scratch.

### Layout file
resources/views/layouts/app.blade.php

### How to extend

```blade
@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
    {{-- your page content here --}}
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // page-specific jQuery here
});
</script>
@endpush
```

### Available sections & stacks

| Name | Type | Purpose |
|------|------|---------|
| `title` | `@section` | Browser tab title |
| `content` | `@section` | Main page content |
| `scripts` | `@push` | Page-specific JS at bottom of body |
| `styles` | `@push` | Page-specific CSS in head (if needed) |

## Model Conventions

### All models must use UUID:

```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Document extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
}
```

### Document model — required scopes:

```php
public function scopeActive($query)   { return $query->where('status', 'active'); }
public function scopeObsolete($query) { return $query->where('status', 'obsolete'); }
public function scopeDraft($query)    { return $query->where('status', 'draft'); }

// Replacement relationships
public function replacedBy(): BelongsTo {
    return $this->belongsTo(Document::class, 'replaced_by_id');
}
public function replaces(): HasOne {
    return $this->hasOne(Document::class, 'replaced_by_id');
}
```

---

## Routes

```php
// routes/web.php

// ── Admin area ────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin,admin_unit,auditor'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('documents', DocumentController::class);
        Route::patch('documents/{document}/publish', [DocumentController::class, 'publish'])->name('documents.publish');
        Route::patch('documents/{document}/obsolete', [DocumentController::class, 'obsolete'])->name('documents.obsolete');
        Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

        Route::resource('users', UserController::class);
        Route::resource('units', UnitController::class);
        Route::resource('document-types', DocumentTypeController::class);
        Route::resource('external-regulations', ExternalRegulationController::class);

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

        Route::get('archive', [ArchiveController::class, 'index'])->name('archive.index');
        Route::patch('archive/{retention}/mark-ready', [ArchiveController::class, 'markReady'])->name('archive.mark-ready');
        Route::patch('archive/{retention}/destroy-record', [ArchiveController::class, 'destroyRecord'])->name('archive.destroy');
    });

// ── User portal ───────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->middleware(['auth'])
    ->group(function () {
        Route::get('documents', [Portal\DocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/{document}', [Portal\DocumentController::class, 'show'])->name('documents.show');
        Route::get('documents/{document}/download', [Portal\DocumentController::class, 'download'])->name('documents.download');
        Route::get('external-regulations', [Portal\ExternalRegulationController::class, 'index'])->name('regulations.index');
    });
```

---

## Service Layer

All business logic goes in Service classes — never in Controllers:

```php
// DocumentService.php — method signatures
class DocumentService
{
    public function publish(Document $document, User $user): void;
    public function setObsolete(Document $document, User $user, string $reason, ?Document $replacement): void;
    public function uploadFile(Document $document, UploadedFile $file, string $type): DocumentFile;
}

// ActivityLogService.php
class ActivityLogService
{
    public function log(User $user, string $action, ?Document $document = null, ?array $detail = null): void;
}
```

---

## jQuery Usage Pattern

jQuery is loaded via CDN in `layouts/admin.blade.php` and `layouts/portal.blade.php`:

```html
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
```

Use jQuery in `public/js/app.js` for shared behavior, and `@push('scripts')` stack
for page-specific scripts:

```php
{{-- In layout --}}
@stack('scripts')

{{-- In a specific blade view --}}
@push('scripts')
<script>
$(document).ready(function () {
    // page-specific jQuery here
});
</script>
@endpush
```

### Modal confirm pattern (jQuery):

```html
{{-- confirm-modal.blade.php component --}}
<div id="confirm-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="font-bold text-lg mb-2" id="modal-title"></h3>
        <p class="text-gray-600 mb-4" id="modal-message"></p>
        <div id="modal-form-fields"></div>
        <div class="flex gap-2 justify-end">
            <button id="modal-cancel" class="px-4 py-2 border rounded">Cancel</button>
            <button id="modal-confirm" class="px-4 py-2 bg-red-600 text-white rounded">Confirm</button>
        </div>
    </div>
</div>
```

```js
// public/js/app.js
function openConfirmModal({ title, message, onConfirm }) {
    $('#modal-title').text(title);
    $('#modal-message').text(message);
    $('#confirm-modal').removeClass('hidden');
    $('#modal-cancel').off('click').on('click', () => $('#confirm-modal').addClass('hidden'));
    $('#modal-confirm').off('click').on('click', () => { onConfirm(); $('#confirm-modal').addClass('hidden'); });
}
```

---

## File Storage

```
storage/app/documents/{unit_code}/{year}/{document_uuid}/
    ├── original.pdf
    └── original.docx   (if uploaded)

storage/app/regulations/{year}/
    └── {filename}.pdf

// Watermarked files are NOT stored — generated on-the-fly per download request
```

---

## Portal Rules

- Document queries in portal **always** use `->active()` scope
- Draft and obsolete documents **never** appear in portal search or browse
- Auditors access obsolete documents through the admin area only

---

## Do NOT Do

```
❌ Do not put business logic in Controllers — use Service classes
❌ Do not hardcode role strings outside Policy classes
❌ Do not store watermarked files — generate on-the-fly
❌ Do not show draft/obsolete documents in the user portal
❌ Do not use integer id() — all primary keys are UUID
❌ Do not use Indonesian for code identifiers (variable, method, column names)
❌ Do not use Livewire, Alpine.js, or any other JS framework — jQuery only
```
