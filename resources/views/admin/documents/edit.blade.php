@extends('layouts.app')

@section('title', 'Edit Dokumen — SIMARS-DOC')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono text-gray-400">{{ $document->number }}</span>
                <span class="ina-badge ina-badge--warning ina-badge--sm">Draft</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Dokumen</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $document->title }}</p>
        </div>
        <a href="{{ route('admin.documents.show', $document) }}" class="ina-button ina-button--secondary ina-button--sm flex items-center gap-2">
            <i class="ti ti-arrow-left text-sm"></i> Kembali
        </a>
    </div>

    {{-- Alerts --}}
    @if (session('error'))
        <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-xl">
            <i class="ti ti-alert-circle text-red-500 text-lg shrink-0 mt-0.5"></i>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-xl">
            <i class="ti ti-alert-circle text-red-500 text-lg shrink-0 mt-0.5"></i>
            <div>
                <p class="text-sm font-medium text-red-700 mb-1">Perbaiki kesalahan berikut:</p>
                <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.documents.update', $document) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ── Section 1: Document Identity ──────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Identitas Dokumen</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Document Number --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="number">
                        Nomor Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('number') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="text" id="number" name="number"
                            class="ina-text-field__input"
                            value="{{ old('number', $document->number) }}" required>
                    </div>
                    @error('number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Extra Numbers (kerjasama) --}}
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700">
                            Nomor Tambahan
                            <span class="text-xs text-gray-400 font-normal ml-1">(untuk dokumen dengan lebih dari satu nomor)</span>
                        </label>
                        <button type="button" id="btn-add-number" class="ina-button ina-button--secondary ina-button--sm">
                            <i class="ti ti-plus text-sm"></i> Tambah Nomor
                        </button>
                    </div>
                    <div id="extra-numbers-list" class="space-y-2">
                        @php
                            $existingExtras = old('extra_numbers') !== null
                                ? old('extra_numbers')
                                : $document->documentNumbers->pluck('number')->toArray();
                        @endphp
                        @foreach ($existingExtras as $num)
                            @if ($num)
                            <div class="flex gap-2 extra-number-row">
                                <div class="ina-text-field flex-1">
                                    <div class="ina-text-field__wrapper">
                                        <input type="text" name="extra_numbers[]" class="ina-text-field__input"
                                            value="{{ $num }}" placeholder="e.g. 002/PKS/2025">
                                    </div>
                                </div>
                                <button type="button" class="ina-button ina-button--secondary ina-button--md remove-extra-number" style="color:#ef4444">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Title --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="title">
                        Judul Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('title') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="text" id="title" name="title"
                            class="ina-text-field__input"
                            value="{{ old('title', $document->title) }}" required maxlength="255">
                    </div>
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Document Type --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="document_type_id">
                        Jenis Dokumen <span class="text-red-500">*</span>
                    </label>
                    <select id="document_type_id" name="document_type_id" class="ts-select {{ $errors->has('document_type_id') ? 'ts-error' : '' }}" required>
                        <option value="">Pilih jenis dokumen...</option>
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('document_type_id', $document->document_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->code }} — {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('document_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Owner Unit (locked) --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label">Unit Pemilik</label>
                    <div class="ina-text-field__wrapper">
                        <input type="text" class="ina-text-field__input bg-gray-50 text-gray-500 cursor-not-allowed"
                            value="{{ $document->ownerUnit?->name }}" disabled>
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Unit pemilik tidak dapat diubah setelah dokumen dibuat.</p>
                </div>

                {{-- Source: default internal, hidden --}}
                <input type="hidden" name="source" value="{{ old('source', $document->source ?? 'internal') }}">

                {{-- Effective Date --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="effective_date">Tanggal Berlaku</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('effective_date') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="date" id="effective_date" name="effective_date"
                            class="ina-text-field__input"
                            value="{{ old('effective_date', $document->effective_date?->format('Y-m-d')) }}">
                    </div>
                    @error('effective_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Expired At --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="expired_at">Masa Berlaku s/d</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('expired_at') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="date" id="expired_at" name="expired_at"
                            class="ina-text-field__input"
                            value="{{ old('expired_at', $document->expired_at?->format('Y-m-d')) }}">
                    </div>
                    @error('expired_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Reminder Months --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="reminder_months">Ingatkan Sebelum Expired (bulan)</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('reminder_months') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="number" id="reminder_months" name="reminder_months"
                            class="ina-text-field__input"
                            placeholder="e.g. 3" min="1" max="60"
                            value="{{ old('reminder_months', $document->reminder_months) }}">
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Opsional. Hanya relevan jika masa berlaku diisi.</p>
                    @error('reminder_months') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Visibility (super_admin only) --}}
                @if (auth()->user()->role->name === 'super_admin')
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="visibility">Visibilitas</label>
                    <select id="visibility" name="visibility" class="ts-select ts-no-search {{ $errors->has('visibility') ? 'ts-error' : '' }}">
                        <option value="public" {{ old('visibility', $document->visibility) === 'public' ? 'selected' : '' }}>Publik — semua pengguna dapat mengakses</option>
                        <option value="restricted" {{ old('visibility', $document->visibility) === 'restricted' ? 'selected' : '' }}>Terbatas — hanya Anda yang dapat mengakses</option>
                    </select>
                    @error('visibility') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

            </div>
        </div>

        {{-- ── Section 2: Additional Info ──────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Tambahan</h2>

            <div class="space-y-4">

                {{-- Description --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="description">Deskripsi</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('description') ? 'ina-text-field__wrapper--error' : '' }}">
                        <textarea id="description" name="description"
                            class="ina-text-field__input min-h-[90px] resize-y"
                            placeholder="Deskripsi singkat isi dokumen (opsional)">{{ old('description', $document->description) }}</textarea>
                    </div>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tags --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="tags">Tags</label>
                    <div class="ina-text-field__wrapper tagify-wrapper {{ $errors->has('tags') ? 'ina-text-field__wrapper--status-error' : '' }}">
                        <input type="text" id="tags" name="tags"
                            placeholder="Ketik lalu tekan Enter atau koma..."
                            value="{{ old('tags', $document->tags) }}">
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Ketik kata kunci lalu tekan Enter atau koma untuk menambahkan tag.</p>
                    @error('tags') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Section 2b: Versi Sebelumnya ───────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Versi Sebelumnya</h2>
            <p class="text-xs text-gray-400 mb-4">Opsional. Isi jika dokumen ini merevisi dokumen yang sudah ada.</p>

            @php $currentParent = $document->parentDocument; @endphp

            {{-- Peringatan: parent sudah obsolete / sudah punya pengganti lain --}}
            @if ($currentParent && $currentParent->status !== 'active')
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700 mb-3 flex items-start gap-2">
                    <i class="ti ti-alert-triangle shrink-0 mt-0.5"></i>
                    <span>Dokumen sebelumnya (<strong>{{ $currentParent->number }}</strong>) sudah obsolet atau telah digantikan. Anda tetap bisa menggantinya di bawah.</span>
                </div>
            @endif

                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="parent_document_id">Dokumen Sebelumnya</label>
                    <select id="parent_document_id" name="parent_document_id" class="ts-select ts-remote">
                        <option value="">— Tidak ada (dokumen baru) —</option>
                        @if ($currentParent)
                            <option value="{{ $currentParent->id }}"
                                {{ old('parent_document_id', $document->parent_document_id) == $currentParent->id ? 'selected' : '' }}>
                                {{ $currentParent->number }} — {{ $currentParent->title }}
                            </option>
                        @endif
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        Saat dokumen ini dipublikasikan, dokumen sebelumnya akan otomatis ditandai sebagai "telah digantikan".
                    </p>
                </div>
        </div>

        {{-- ── Section 3: File Replacement ─────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Berkas Dokumen</h2>
            <p class="text-xs text-gray-400 mb-4">Kosongkan untuk mempertahankan berkas yang ada. Upload baru untuk mengganti.</p>

            <div class="space-y-5">

                {{-- PDF --}}
                <div>
                    @php $currentPdf = $document->files->firstWhere('file_type', 'pdf'); @endphp
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="pdf_file">
                        File PDF
                        <span class="text-gray-400 font-normal ml-1">(opsional penggantian, maks. 20MB)</span>
                    </label>
                    @if ($currentPdf)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg mb-2">
                            <i class="ti ti-file-type-pdf text-red-500 text-xl"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $currentPdf->original_filename }}</p>
                                <p class="text-xs text-gray-400">{{ number_format($currentPdf->file_size / 1024 / 1024, 2) }} MB — File saat ini</p>
                            </div>
                        </div>
                    @endif
                    <div class="border-2 border-dashed rounded-xl p-4 {{ $errors->has('pdf_file') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50' }} transition-colors">
                        <input type="file" id="pdf_file" name="pdf_file" accept=".pdf,application/pdf" class="hidden">
                        <label for="pdf_file" class="flex flex-col items-center justify-center gap-2 cursor-pointer py-2">
                            <i class="ti ti-file-type-pdf text-3xl text-red-400"></i>
                            <span class="text-sm font-medium text-gray-600" id="pdf-label">Klik untuk pilih PDF pengganti</span>
                            <span class="text-xs text-gray-400">Format: .pdf — Maks. 20MB</span>
                        </label>
                    </div>
                    @error('pdf_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- DOCX --}}
                <div>
                    @php $currentDocx = $document->files->firstWhere('file_type', 'docx'); @endphp
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="docx_file">
                        File DOCX
                        <span class="text-gray-400 font-normal ml-1">(opsional, maks. 20MB)</span>
                    </label>
                    @if ($currentDocx)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg mb-2">
                            <i class="ti ti-file-type-docx text-blue-500 text-xl"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $currentDocx->original_filename }}</p>
                                <p class="text-xs text-gray-400">{{ number_format($currentDocx->file_size / 1024 / 1024, 2) }} MB — File saat ini</p>
                            </div>
                        </div>
                    @endif
                    <div class="border-2 border-dashed rounded-xl p-4 {{ $errors->has('docx_file') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50' }} transition-colors">
                        <input type="file" id="docx_file" name="docx_file"
                            accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            class="hidden">
                        <label for="docx_file" class="flex flex-col items-center justify-center gap-2 cursor-pointer py-2">
                            <i class="ti ti-file-type-docx text-3xl text-blue-400"></i>
                            <span class="text-sm font-medium text-gray-600" id="docx-label">Klik untuk pilih DOCX pengganti</span>
                            <span class="text-xs text-gray-400">Format: .docx — Maks. 20MB</span>
                        </label>
                    </div>
                    @error('docx_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Form Actions ─────────────────────────────────────────────── --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.documents.show', $document) }}" class="ina-button ina-button--secondary ina-button--md">
                Batal
            </a>
            <button type="submit" class="ina-button ina-button--primary ina-button--md flex items-center gap-2" id="submit-btn">
                <i class="ti ti-device-floppy"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>

    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<style>
/* ── Tagify ── */
.tagify-wrapper { height: auto !important; min-height: 40px; padding: 0.25rem 0.375rem; flex-wrap: wrap; align-items: center; gap: 0; }
.tagify-wrapper .tagify { border: none !important; box-shadow: none !important; background: transparent !important; padding: 0; min-height: 28px; width: 100%; flex: 1; }
.tagify-wrapper .tagify:focus-within { outline: none; }
.tagify__tag { margin: 2px; }
.tagify__tag > div { background: #e0f2fe; border-radius: 0.375rem; padding: 2px 6px; color: #0369a1; font-size: 0.75rem; }
.tagify__tag > div::before { box-shadow: none !important; }
.tagify__tag__removeBtn { color: #0369a1; }
.tagify__tag__removeBtn:hover { background: #0369a1; color: white; }
.tagify__input { min-width: 60px; font-size: 0.875rem; color: var(--ina-content-primary, #1f1f1f); }
.tagify__input::before { color: var(--ina-content-tertiary, #a3a3a3); }
/* ── Tom Select — full custom theme, no CDN CSS ── */
.ts-wrapper { position: relative; width: 100%; }
.ts-wrapper *, .ts-wrapper *::before, .ts-wrapper *::after { box-sizing: border-box; }
.ts-hidden-accessible { position: absolute !important; width: 1px !important; height: 1px !important; overflow: hidden !important; clip: rect(0 0 0 0) !important; }
.ts-control { display: flex; flex-wrap: wrap; align-items: center; gap: 4px; background: #fff; border: 1px solid #e5e5e5; border-radius: 0.5rem; padding: 0 0.75rem; min-height: 40px; cursor: pointer; transition: border-color .15s; user-select: none; }
.ts-wrapper.focus .ts-control { border-color: #2596be; box-shadow: 0 0 0 3px rgba(37,150,190,.15); outline: none; }
.ts-wrapper.ts-error .ts-control { border-color: #f02d2d; }
.ts-wrapper.disabled .ts-control { background: #f5f5f5; opacity: .6; cursor: not-allowed; }
.ts-control .item { font-size: 0.875rem; color: #1f1f1f; line-height: 1.5; }
.ts-control .ts-placeholder { font-size: 0.875rem; color: #a3a3a3; line-height: 1.5; flex: 1; }
.ts-control input { flex: 1; min-width: 60px; border: none; background: transparent; outline: none; font-size: 0.875rem; color: #1f1f1f; padding: 0; line-height: 1.5; font-family: inherit; }
.ts-control input::placeholder { color: #a3a3a3; }
.ts-wrapper.single .ts-control::after { content: ''; flex-shrink: 0; width: 0; height: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 5px solid #9ca3af; margin-left: auto; transition: transform .15s; }
.ts-wrapper.single.open .ts-control::after { transform: rotate(180deg); }
.ts-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 1000; background: #fff; border: 1px solid #e5e5e5; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,.08); overflow: hidden; }
.ts-dropdown-content { overflow-y: auto; max-height: 220px; }
.ts-dropdown .option { padding: 8px 12px; font-size: 0.875rem; color: #1f1f1f; cursor: pointer; }
.ts-dropdown .option:hover, .ts-dropdown .option.active { background: #f0f9ff; color: #0369a1; }
.ts-dropdown .option.selected { background: #e0f2fe; color: #0369a1; font-weight: 500; }
.ts-dropdown .no-results, .ts-dropdown .loading { padding: 8px 12px; font-size: 0.875rem; color: #a3a3a3; }
.ts-dropdown input { display: block; width: 100%; border: none; border-bottom: 1px solid #f0f0f0; padding: 8px 12px; font-size: 0.875rem; outline: none; font-family: inherit; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
// Tagify
new Tagify(document.getElementById('tags'), {
    delimiters: ',| ',
    trim: true,
    originalInputValueFormat: values => values.map(v => v.value).join(', '),
});

// Tom Select — client-side
document.querySelectorAll('.ts-select:not(.ts-no-search):not(.ts-remote)').forEach(el => {
    new TomSelect(el, { placeholder: el.options[0]?.text ?? '...' });
});

// Tom Select — no search
document.querySelectorAll('.ts-select.ts-no-search').forEach(el => {
    new TomSelect(el, { controlInput: null });
});

// Tom Select — server-side
document.querySelectorAll('.ts-select.ts-remote').forEach(el => {
    new TomSelect(el, {
        valueField: 'value',
        labelField: 'text',
        searchField: 'text',
        placeholder: '— Cari dokumen aktif...',
        load(query, callback) {
            const exclude = '{{ $document->id }}';
            fetch(`{{ route('admin.documents.search-parents') }}?q=${encodeURIComponent(query)}&exclude=${exclude}`)
                .then(r => r.json())
                .then(callback)
                .catch(() => callback());
        },
        preload: 'focus',
        shouldLoad: () => true,
    });
});
</script>
<script>
$(document).ready(function () {
    function updateFileLabel(inputId, labelId, defaultText) {
        $('#' + inputId).on('change', function () {
            const file = this.files[0];
            if (file) {
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                $('#' + labelId).text(file.name + ' (' + sizeMB + ' MB)').addClass('text-green-600').removeClass('text-gray-600');
            } else {
                $('#' + labelId).text(defaultText).removeClass('text-green-600').addClass('text-gray-600');
            }
        });
    }

    updateFileLabel('pdf_file', 'pdf-label', 'Klik untuk pilih PDF pengganti');
    updateFileLabel('docx_file', 'docx-label', 'Klik untuk pilih DOCX pengganti');

    $('form').on('submit', function () {
        $('#submit-btn').prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i> <span>Menyimpan...</span>');
    });

    // Extra numbers
    const extraNumberRowHtml = () => `<div class="flex gap-2 extra-number-row">
        <div class="ina-text-field flex-1">
            <div class="ina-text-field__wrapper">
                <input type="text" name="extra_numbers[]" class="ina-text-field__input" placeholder="e.g. 002/PKS/2025">
            </div>
        </div>
        <button type="button" class="ina-button ina-button--secondary ina-button--md remove-extra-number" style="color:#ef4444">
            <i class="ti ti-trash text-sm"></i>
        </button>
    </div>`;

    $('#btn-add-number').on('click', function () {
        $('#extra-numbers-list').append(extraNumberRowHtml());
        $('#extra-numbers-list .extra-number-row:last input').focus();
    });

    $(document).on('click', '.remove-extra-number', function () {
        $(this).closest('.extra-number-row').remove();
    });
});
</script>
@endpush
