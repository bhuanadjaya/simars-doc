@extends('layouts.app')

@section('title', 'Upload Dokumen — SIMARS-DOC')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Upload Dokumen Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Dokumen akan disimpan sebagai <span class="font-medium text-yellow-600">Draft</span> hingga dipublikasikan.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="ina-button ina-button--secondary ina-button--sm flex items-center gap-2">
            <i class="ti ti-arrow-left text-sm"></i> Kembali
        </a>
    </div>

    {{-- Error / Success alerts --}}
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
                <p class="text-sm font-medium text-red-700 mb-1">Please fix the following errors:</p>
                <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
        @csrf

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
                            placeholder="e.g. 001/SPO/IGD/2025"
                            value="{{ old('number') }}" required>
                    </div>
                    @error('number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Title --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="title">
                        Judul Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('title') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="text" id="title" name="title"
                            class="ina-text-field__input"
                            placeholder="Judul lengkap dokumen"
                            value="{{ old('title') }}" required maxlength="255">
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
                            <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->code }} — {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('document_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Owner Unit --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="owner_unit_id">
                        Unit Pemilik <span class="text-red-500">*</span>
                    </label>
                    @if ($user->role->name === 'admin_unit')
                        <input type="hidden" name="owner_unit_id" value="{{ $user->unit_id }}">
                        <div class="ina-text-field__wrapper">
                            <input type="text" class="ina-text-field__input bg-gray-50 text-gray-500 cursor-not-allowed"
                                value="{{ $user->unit->name ?? $user->unit_id }}" disabled>
                        </div>
                    @else
                        <select id="owner_unit_id" name="owner_unit_id" class="ts-select {{ $errors->has('owner_unit_id') ? 'ts-error' : '' }}" required>
                            <option value="">Pilih unit...</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('owner_unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->code }} — {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('owner_unit_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>

                {{-- Source: default internal, hidden --}}
                <input type="hidden" name="source" value="internal">

                {{-- Effective Date --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="effective_date">Tanggal Berlaku</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('effective_date') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="date" id="effective_date" name="effective_date"
                            class="ina-text-field__input"
                            value="{{ old('effective_date') }}">
                    </div>
                    @error('effective_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Expired At --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="expired_at">Masa Berlaku s/d</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('expired_at') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="date" id="expired_at" name="expired_at"
                            class="ina-text-field__input"
                            value="{{ old('expired_at') }}">
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
                            value="{{ old('reminder_months') }}">
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Opsional. Hanya relevan jika masa berlaku diisi.</p>
                    @error('reminder_months') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Visibility (super_admin only) --}}
                @if ($user->role->name === 'super_admin')
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="visibility">Visibilitas</label>
                    <select id="visibility" name="visibility" class="ts-select ts-no-search {{ $errors->has('visibility') ? 'ts-error' : '' }}">
                        <option value="public" {{ old('visibility', 'public') === 'public' ? 'selected' : '' }}>Publik — semua pengguna dapat mengakses</option>
                        <option value="restricted" {{ old('visibility') === 'restricted' ? 'selected' : '' }}>Terbatas — hanya Anda yang dapat mengakses</option>
                    </select>
                    @error('visibility') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

            </div>
        </div>

        {{-- ── Section: Status Upload ──────────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Status Dokumen</h2>
            <p class="text-xs text-gray-400 mb-4">Pilih status awal dokumen yang akan diupload.</p>

            <div class="flex flex-col gap-3">
                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors status-option" for="status-active">
                    <input type="radio" id="status-active" name="target_status" value="active"
                        {{ old('target_status', 'active') === 'active' ? 'checked' : '' }}>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Aktif (melalui Draft)</p>
                        <p class="text-xs text-gray-500">Dokumen disimpan sebagai Draft, kemudian dipublikasikan secara terpisah.</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors status-option" for="status-obsolete">
                    <input type="radio" id="status-obsolete" name="target_status" value="obsolete"
                        {{ old('target_status') === 'obsolete' ? 'checked' : '' }}>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Langsung Obsolete</p>
                        <p class="text-xs text-gray-500">Dokumen disimpan langsung sebagai Obsolete (untuk digitalisasi arsip lama).</p>
                    </div>
                </label>
            </div>

            <div id="obsolete-fields" class="{{ old('target_status') === 'obsolete' ? '' : 'hidden' }} mt-4 space-y-4 pt-4 border-t border-gray-100">
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="obsolete_reason">
                        Alasan Obsolete <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('obsolete_reason') ? 'ina-text-field__wrapper--error' : '' }}">
                        <textarea id="obsolete_reason" name="obsolete_reason"
                            class="ina-text-field__input min-h-[80px] resize-y"
                            placeholder="Tuliskan alasan dokumen ini langsung berstatus obsolete...">{{ old('obsolete_reason') }}</textarea>
                    </div>
                    @error('obsolete_reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="obsolete_date">
                        Tanggal Obsolete <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('obsolete_date') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="date" id="obsolete_date" name="obsolete_date"
                            class="ina-text-field__input"
                            value="{{ old('obsolete_date', now()->format('Y-m-d')) }}">
                    </div>
                    @error('obsolete_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
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
                            placeholder="Deskripsi singkat isi dokumen (opsional)">{{ old('description') }}</textarea>
                    </div>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tags --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="tags">Tags</label>
                    <div class="ina-text-field__wrapper tagify-wrapper {{ $errors->has('tags') ? 'ina-text-field__wrapper--status-error' : '' }}">
                        <input type="text" id="tags" name="tags"
                            placeholder="Ketik lalu tekan Enter atau koma..."
                            value="{{ old('tags') }}">
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Ketik kata kunci lalu tekan Enter atau koma untuk menambahkan tag.</p>
                    @error('tags') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Section 3: Versi Sebelumnya ────────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Versi Sebelumnya</h2>
            <p class="text-xs text-gray-400 mb-4">Opsional. Isi jika dokumen ini merevisi dokumen yang sudah ada.</p>

            <div class="ina-text-field">
                <label class="ina-text-field__label" for="parent_document_id">Dokumen Sebelumnya</label>
                <select id="parent_document_id" name="parent_document_id" class="ts-select ts-remote">
                    <option value="">— Tidak ada (dokumen baru) —</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">
                    Saat dokumen ini dipublikasikan, dokumen sebelumnya akan otomatis ditandai sebagai "telah digantikan".
                </p>
            </div>
        </div>

        {{-- ── Section 4: File Upload ───────────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Berkas Dokumen</h2>

            <div class="space-y-4">

                {{-- PDF Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="pdf_file">
                        File PDF <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal ml-1">(maks. 20MB)</span>
                    </label>
                    <div class="border-2 border-dashed rounded-xl p-4 {{ $errors->has('pdf_file') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50' }} transition-colors cursor-pointer" id="pdf-drop-zone">
                        <input type="file" id="pdf_file" name="pdf_file" accept=".pdf,application/pdf"
                            class="hidden" required>
                        <label for="pdf_file" class="flex flex-col items-center justify-center gap-2 cursor-pointer py-2">
                            <i class="ti ti-file-type-pdf text-3xl text-red-500"></i>
                            <span class="text-sm font-medium text-gray-700" id="pdf-label">Klik untuk pilih file PDF</span>
                            <span class="text-xs text-gray-400">Format: .pdf — Maks. 20MB</span>
                        </label>
                    </div>
                    @error('pdf_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- DOCX Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="docx_file">
                        File DOCX
                        <span class="text-gray-400 font-normal ml-1">(opsional, maks. 20MB)</span>
                    </label>
                    <div class="border-2 border-dashed rounded-xl p-4 {{ $errors->has('docx_file') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50' }} transition-colors cursor-pointer" id="docx-drop-zone">
                        <input type="file" id="docx_file" name="docx_file"
                            accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            class="hidden">
                        <label for="docx_file" class="flex flex-col items-center justify-center gap-2 cursor-pointer py-2">
                            <i class="ti ti-file-type-docx text-3xl text-blue-500"></i>
                            <span class="text-sm font-medium text-gray-700" id="docx-label">Klik untuk pilih file DOCX (opsional)</span>
                            <span class="text-xs text-gray-400">Format: .docx — Maks. 20MB</span>
                        </label>
                    </div>
                    @error('docx_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Form Actions ─────────────────────────────────────────────── --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.dashboard') }}" class="ina-button ina-button--secondary ina-button--md">
                Batal
            </a>
            <button type="submit" class="ina-button ina-button--primary ina-button--md flex items-center gap-2" id="submit-btn">
                <i class="ti ti-upload"></i>
                <span>Upload Dokumen</span>
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
/* Original select hidden by JS; accessibility span */
.ts-hidden-accessible { position: absolute !important; width: 1px !important; height: 1px !important; overflow: hidden !important; clip: rect(0 0 0 0) !important; }
/* Control */
.ts-control { display: flex; flex-wrap: wrap; align-items: center; gap: 4px; background: #fff; border: 1px solid #e5e5e5; border-radius: 0.5rem; padding: 0 0.75rem; min-height: 40px; cursor: pointer; transition: border-color .15s; user-select: none; }
.ts-wrapper.focus .ts-control { border-color: #2596be; box-shadow: 0 0 0 3px rgba(37,150,190,.15); outline: none; }
.ts-wrapper.ts-error .ts-control { border-color: #f02d2d; }
.ts-wrapper.disabled .ts-control { background: #f5f5f5; opacity: .6; cursor: not-allowed; }
.ts-control .item { font-size: 0.875rem; color: #1f1f1f; line-height: 1.5; }
.ts-control .ts-placeholder { font-size: 0.875rem; color: #a3a3a3; line-height: 1.5; flex: 1; }
.ts-control input { flex: 1; min-width: 60px; border: none; background: transparent; outline: none; font-size: 0.875rem; color: #1f1f1f; padding: 0; line-height: 1.5; font-family: inherit; }
.ts-control input::placeholder { color: #a3a3a3; }
/* Caret arrow */
.ts-wrapper.single .ts-control::after { content: ''; flex-shrink: 0; width: 0; height: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 5px solid #9ca3af; margin-left: auto; transition: transform .15s; }
.ts-wrapper.single.open .ts-control::after { transform: rotate(180deg); }
/* Dropdown */
.ts-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 1000; background: #fff; border: 1px solid #e5e5e5; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,.08); overflow: hidden; }
.ts-dropdown-content { overflow-y: auto; max-height: 220px; }
.ts-dropdown .option { padding: 8px 12px; font-size: 0.875rem; color: #1f1f1f; cursor: pointer; }
.ts-dropdown .option:hover, .ts-dropdown .option.active { background: #f0f9ff; color: #0369a1; }
.ts-dropdown .option.selected { background: #e0f2fe; color: #0369a1; font-weight: 500; }
.ts-dropdown .no-results, .ts-dropdown .loading { padding: 8px 12px; font-size: 0.875rem; color: #a3a3a3; }
/* Search input (inside dropdown for server-side) */
.ts-dropdown .ts-dropdown-header { border-bottom: 1px solid #f0f0f0; }
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

// Tom Select — client-side (with search)
document.querySelectorAll('.ts-select:not(.ts-no-search):not(.ts-remote)').forEach(el => {
    new TomSelect(el, { placeholder: el.options[0]?.text ?? '...' });
});

// Tom Select — no search (small dropdowns)
document.querySelectorAll('.ts-select.ts-no-search').forEach(el => {
    new TomSelect(el, { controlInput: null });
});

// Tom Select — server-side (parent_document_id)
document.querySelectorAll('.ts-select.ts-remote').forEach(el => {
    new TomSelect(el, {
        valueField: 'value',
        labelField: 'text',
        searchField: 'text',
        placeholder: '— Cari dokumen aktif...',
        load(query, callback) {
            fetch(`{{ route('admin.documents.search-parents') }}?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(callback)
                .catch(() => callback());
        },
        preload: 'focus',
        shouldLoad: () => true,
    });
});

$(document).ready(function () {
    // Toggle obsolete fields
    function toggleObsoleteFields() {
        const isObsolete = $('input[name="target_status"]:checked').val() === 'obsolete';
        $('#obsolete-fields').toggleClass('hidden', !isObsolete);
        $('#obsolete_reason').prop('required', isObsolete);
        $('#obsolete_date').prop('required', isObsolete);
    }
    $('input[name="target_status"]').on('change', toggleObsoleteFields);
    toggleObsoleteFields();

    // File input label updater
    function updateFileLabel(inputId, labelId) {
        $('#' + inputId).on('change', function () {
            const file = this.files[0];
            if (file) {
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                $('#' + labelId).text(file.name + ' (' + sizeMB + ' MB)').addClass('text-green-600').removeClass('text-gray-700');
            } else {
                $('#' + labelId).text(inputId === 'pdf_file' ? 'Klik untuk pilih file PDF' : 'Klik untuk pilih file DOCX (opsional)').removeClass('text-green-600').addClass('text-gray-700');
            }
        });
    }

    updateFileLabel('pdf_file', 'pdf-label');
    updateFileLabel('docx_file', 'docx-label');

    $('form').on('submit', function () {
        $('#submit-btn').prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i> <span>Mengupload...</span>');
    });
});
</script>
@endpush
