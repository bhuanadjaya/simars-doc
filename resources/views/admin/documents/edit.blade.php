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
                    <div class="ina-text-field__wrapper {{ $errors->has('document_type_id') ? 'ina-text-field__wrapper--error' : '' }}">
                        <select id="document_type_id" name="document_type_id" class="ina-text-field__input" required>
                            <option value="">Pilih jenis dokumen...</option>
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ old('document_type_id', $document->document_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->code }} — {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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

                {{-- Source --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="source">
                        Sumber <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('source') ? 'ina-text-field__wrapper--error' : '' }}">
                        <select id="source" name="source" class="ina-text-field__input" required>
                            <option value="internal" {{ old('source', $document->source) == 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="external" {{ old('source', $document->source) == 'external' ? 'selected' : '' }}>Eksternal</option>
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
                            value="{{ old('effective_date', $document->effective_date?->format('Y-m-d')) }}">
                    </div>
                    @error('effective_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                            placeholder="Deskripsi singkat isi dokumen (opsional)">{{ old('description', $document->description) }}</textarea>
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
                            value="{{ old('tags', $document->tags) }}">
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Kata kunci untuk pencarian, pisahkan dengan koma.</p>
                    @error('tags') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Section 2b: Versi Sebelumnya ───────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Versi Sebelumnya</h2>
            <p class="text-xs text-gray-400 mb-4">Opsional. Isi jika dokumen ini merevisi dokumen yang sudah ada.</p>

            @php $currentParent = $document->parentDocument; @endphp

            {{-- Parent sudah ada tapi tidak tersedia di dropdown (sudah obsolete/sudah punya pengganti) --}}
            @if ($currentParent && ! $availableParents->contains('id', $currentParent->id))
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700 mb-3 flex items-start gap-2">
                    <i class="ti ti-alert-triangle shrink-0 mt-0.5"></i>
                    <div>
                        Dokumen sebelumnya saat ini (<strong>{{ $currentParent->number }} — {{ $currentParent->title }}</strong>)
                        sudah tidak tersedia untuk dipilih (sudah obsolet atau sudah punya pengganti).
                        Anda bisa menghapus pilihan ini atau biarkan.
                    </div>
                </div>
                <input type="hidden" name="parent_document_id" id="parent_document_id_hidden" value="{{ old('parent_document_id', $currentParent->id) }}">
                <div class="flex items-center gap-3">
                    <div class="ina-text-field flex-1">
                        <div class="ina-text-field__wrapper">
                            <input type="text" class="ina-text-field__input bg-gray-50 text-gray-500 cursor-not-allowed"
                                value="{{ $currentParent->number }} — {{ $currentParent->title }}" disabled>
                        </div>
                    </div>
                    <button type="button" id="btn-clear-parent"
                        class="ina-button ina-button--secondary ina-button--sm text-red-500 shrink-0">
                        <i class="ti ti-x text-sm"></i> Hapus
                    </button>
                </div>
            @else
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="parent_document_id">Dokumen Sebelumnya</label>
                    <div class="ina-text-field__wrapper">
                        <select id="parent_document_id" name="parent_document_id" class="ina-text-field__input">
                            <option value="">— Tidak ada (dokumen baru) —</option>
                            @foreach ($availableParents as $parent)
                                <option value="{{ $parent->id }}"
                                    {{ old('parent_document_id', $document->parent_document_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->number }} — {{ Str::limit($parent->title, 70) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        Saat dokumen ini dipublikasikan, dokumen sebelumnya akan otomatis ditandai sebagai "telah digantikan".
                    </p>
                </div>
            @endif
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

@push('scripts')
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

    // Clear locked parent
    $('#btn-clear-parent').on('click', function () {
        $('#parent_document_id_hidden').val('');
        $(this).closest('.flex').replaceWith(
            '<p class="text-sm text-gray-400 italic">Dokumen sebelumnya telah dihapus.</p>'
        );
    });

    $('form').on('submit', function () {
        $('#submit-btn').prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i> <span>Menyimpan...</span>');
    });
});
</script>
@endpush
