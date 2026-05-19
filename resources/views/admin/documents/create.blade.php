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
                    <div class="ina-text-field__wrapper {{ $errors->has('document_type_id') ? 'ina-text-field__wrapper--error' : '' }}">
                        <select id="document_type_id" name="document_type_id" class="ina-text-field__input" required>
                            <option value="">Pilih jenis dokumen...</option>
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->code }} — {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
                        <div class="ina-text-field__wrapper {{ $errors->has('owner_unit_id') ? 'ina-text-field__wrapper--error' : '' }}">
                            <select id="owner_unit_id" name="owner_unit_id" class="ina-text-field__input" required>
                                <option value="">Pilih unit...</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('owner_unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->code }} — {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('owner_unit_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>

                {{-- Source --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="source">
                        Sumber <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('source') ? 'ina-text-field__wrapper--error' : '' }}">
                        <select id="source" name="source" class="ina-text-field__input" required>
                            <option value="">Pilih sumber...</option>
                            <option value="internal" {{ old('source') == 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="external" {{ old('source') == 'external' ? 'selected' : '' }}>Eksternal</option>
                        </select>
                    </div>
                    @error('source') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

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

            </div>
        </div>

        {{-- ── Section 2: Revision Info ────────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Revisi</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Revision Number --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="revision_number">Nomor Revisi</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('revision_number') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="number" id="revision_number" name="revision_number"
                            class="ina-text-field__input"
                            min="0" value="{{ old('revision_number', 0) }}"
                            placeholder="0 = dokumen asli">
                    </div>
                    <p class="text-gray-400 text-xs mt-1">0 = dokumen asli, 1 = Revisi 01, dst.</p>
                    @error('revision_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Parent Document --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="parent_document_id">Versi Sebelumnya (opsional)</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('parent_document_id') ? 'ina-text-field__wrapper--error' : '' }}">
                        <select id="parent_document_id" name="parent_document_id" class="ina-text-field__input">
                            <option value="">— Dokumen baru (bukan revisi) —</option>
                            @foreach ($documents as $doc)
                                <option value="{{ $doc->id }}" {{ old('parent_document_id') == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->number }} — {{ Str::limit($doc->title, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('parent_document_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Section 3: Additional Info ──────────────────────────────── --}}
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
                    <div class="ina-text-field__wrapper {{ $errors->has('tags') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="text" id="tags" name="tags"
                            class="ina-text-field__input"
                            placeholder="e.g. triase, igd, emergency (pisahkan dengan koma)"
                            value="{{ old('tags') }}">
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Kata kunci untuk pencarian, pisahkan dengan koma.</p>
                    @error('tags') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

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

@push('scripts')
<script>
$(document).ready(function () {
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

    // Disable submit button on form submit to prevent double-click
    $('form').on('submit', function () {
        $('#submit-btn').prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i> <span>Mengupload...</span>');
    });
});
</script>
@endpush
