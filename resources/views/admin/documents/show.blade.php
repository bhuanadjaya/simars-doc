@extends('layouts.app')

@section('title', $document->title . ' — SIMARS-DOC')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-start justify-between mb-6">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.dashboard') }}" class="ina-button ina-button--secondary ina-button--sm mt-0.5 flex items-center gap-1">
                <i class="ti ti-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-mono text-gray-400">{{ $document->number }}</span>
                    <span class="ina-badge {{ $document->status === 'active' ? 'ina-badge--positive' : ($document->status === 'obsolete' ? 'ina-badge--destructive' : 'ina-badge--warning') }} ina-badge--sm">
                        {{ ucfirst($document->status) }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-gray-900">{{ $document->title }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $document->documentType?->name }} &middot; {{ $document->ownerUnit?->name }}
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            @if ($document->status === 'draft')
                @can('publish', $document)
                    <a href="{{ route('admin.documents.edit', $document) }}"
                        class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1.5">
                        <i class="ti ti-pencil text-sm"></i> Edit
                    </a>
                    <button type="button" id="btn-publish"
                        class="ina-button ina-button--primary ina-button--sm flex items-center gap-1.5">
                        <i class="ti ti-send text-sm"></i> Publikasikan
                    </button>
                    <form id="form-publish" method="POST"
                        action="{{ route('admin.documents.publish', $document) }}" class="hidden">
                        @csrf @method('PATCH')
                    </form>
                @endcan

                @can('delete', $document)
                    <button type="button" id="btn-delete"
                        class="ina-button ina-button--sm flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-300 hover:bg-red-100">
                        <i class="ti ti-trash text-sm"></i> Hapus
                    </button>
                    <form id="form-delete" method="POST"
                        action="{{ route('admin.documents.destroy', $document) }}" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                @endcan
            @endif

            @if ($document->status === 'active')
                @can('obsolete', $document)
                    <button type="button" id="btn-obsolete"
                        class="ina-button ina-button--sm flex items-center gap-1.5 bg-orange-50 text-orange-700 border border-orange-300 hover:bg-orange-100">
                        <i class="ti ti-archive text-sm"></i> Set Obsolet
                    </button>
                @endcan
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-start gap-3 p-4 mb-5 bg-green-50 border border-green-200 rounded-xl">
            <i class="ti ti-circle-check text-green-500 text-lg shrink-0 mt-0.5"></i>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-xl">
            <i class="ti ti-alert-circle text-red-500 text-lg shrink-0 mt-0.5"></i>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Document Metadata --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Dokumen</h2>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Nomor Dokumen</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $document->number }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Jenis Dokumen</dt>
                <dd class="font-medium text-gray-900 mt-0.5">
                    {{ $document->documentType?->code }} — {{ $document->documentType?->name }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Unit Pemilik</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $document->ownerUnit?->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Sumber</dt>
                <dd class="font-medium text-gray-900 mt-0.5 capitalize">{{ $document->source }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Nomor Revisi</dt>
                <dd class="font-medium text-gray-900 mt-0.5">
                    {{ $document->revision_number == 0 ? 'Original' : 'Rev. ' . str_pad($document->revision_number, 2, '0', STR_PAD_LEFT) }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Tanggal Berlaku</dt>
                <dd class="font-medium text-gray-900 mt-0.5">
                    {{ $document->effective_date ? $document->effective_date->format('d/m/Y') : '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Diupload Oleh</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $document->uploader?->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Dibuat Pada</dt>
                <dd class="font-medium text-gray-900 mt-0.5">
                    {{ $document->created_at->format('d/m/Y H:i') }}
                </dd>
            </div>
            @if ($document->description)
                <div class="sm:col-span-2">
                    <dt class="text-gray-400 text-xs uppercase tracking-wide">Deskripsi</dt>
                    <dd class="text-gray-700 mt-0.5 whitespace-pre-line">{{ $document->description }}</dd>
                </div>
            @endif
            @if ($document->tags)
                <div class="sm:col-span-2">
                    <dt class="text-gray-400 text-xs uppercase tracking-wide mb-1">Tags</dt>
                    <dd class="flex flex-wrap gap-1.5">
                        @foreach (explode(',', $document->tags) as $tag)
                            <span class="ina-badge ina-badge--info ina-badge--sm">{{ trim($tag) }}</span>
                        @endforeach
                    </dd>
                </div>
            @endif
        </dl>

        {{-- Revision chain --}}
        @if ($document->parentDocument)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Merevisi Dokumen</p>
                <p class="text-sm text-blue-700">
                    <i class="ti ti-arrow-back-up mr-1"></i>
                    {{ $document->parentDocument->number }} — {{ $document->parentDocument->title }}
                </p>
            </div>
        @endif
    </div>

    {{-- Files --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Berkas</h2>

        @if ($document->files->isEmpty())
            <p class="text-sm text-gray-400">Belum ada berkas yang diupload.</p>
        @else
            <div class="space-y-3">
                @foreach ($document->files as $file)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center gap-3">
                            <i class="ti {{ $file->file_type === 'pdf' ? 'ti-file-type-pdf text-red-500' : 'ti-file-type-docx text-blue-500' }} text-2xl"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $file->original_filename }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ strtoupper($file->file_type) }} &middot;
                                    {{ number_format($file->file_size / 1024 / 1024, 2) }} MB
                                </p>
                            </div>
                        </div>
                        <span class="ina-badge ina-badge--info ina-badge--sm uppercase">{{ $file->file_type }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

{{-- Obsolete modal --}}
@if ($document->status === 'active')
    @can('obsolete', $document)
        <div id="modal-obsolete" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Set Dokumen Obsolet</h3>
                    <button type="button" id="modal-obsolete-close" class="text-gray-400 hover:text-gray-600">
                        <i class="ti ti-x text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.documents.obsolete', $document) }}" id="form-obsolete">
                    @csrf @method('PATCH')

                    <div class="px-6 py-5 space-y-4">
                        <div class="p-3 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-700">
                            <i class="ti ti-alert-triangle mr-1"></i>
                            Tindakan ini tidak dapat dibatalkan. Dokumen akan berubah status menjadi <strong>Obsolet</strong>.
                        </div>

                        {{-- Obsolete reason --}}
                        <div class="ina-text-field">
                            <label class="ina-text-field__label" for="obsolete_reason">
                                Alasan Obsolet <span class="text-red-500">*</span>
                            </label>
                            <div class="ina-text-field__wrapper">
                                <textarea id="obsolete_reason" name="obsolete_reason"
                                    class="ina-text-field__input min-h-[90px] resize-y"
                                    placeholder="Tuliskan alasan dokumen ini dinyatakan obsolet..."
                                    required maxlength="1000">{{ old('obsolete_reason') }}</textarea>
                            </div>
                        </div>

                        {{-- Replaced by --}}
                        <div class="ina-text-field">
                            <label class="ina-text-field__label" for="replaced_by_id">
                                Digantikan Oleh <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <div class="ina-text-field__wrapper">
                                <select id="replaced_by_id" name="replaced_by_id" class="ina-text-field__input">
                                    <option value="">— Tidak ada pengganti —</option>
                                    @foreach ($activeDocuments as $doc)
                                        <option value="{{ $doc->id }}">
                                            {{ $doc->number }} — {{ Str::limit($doc->title, 60) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Dokumen aktif yang menggantikan dokumen ini.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                        <button type="button" id="modal-obsolete-cancel"
                            class="ina-button ina-button--secondary ina-button--sm">Batal</button>
                        <button type="submit" id="btn-obsolete-submit"
                            class="ina-button ina-button--sm flex items-center gap-1.5 bg-orange-600 text-white border border-orange-700 hover:bg-orange-700">
                            <i class="ti ti-archive text-sm"></i> Konfirmasi Obsolet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@endif

@push('scripts')
<script>
$(document).ready(function () {
    // Publish confirm
    $('#btn-publish').on('click', function () {
        if (confirm('Publikasikan dokumen ini? Status akan berubah menjadi Aktif dan tidak dapat dikembalikan ke Draft.')) {
            $(this).prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i> Memproses...');
            $('#form-publish').submit();
        }
    });

    // Delete confirm
    $('#btn-delete').on('click', function () {
        if (confirm('Hapus dokumen ini secara permanen? Tindakan ini tidak dapat dibatalkan.')) {
            $(this).prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i>');
            $('#form-delete').submit();
        }
    });

    // Obsolete modal
    $('#btn-obsolete').on('click', function () {
        $('#modal-obsolete').removeClass('hidden');
        $('#obsolete_reason').focus();
    });

    $('#modal-obsolete-close, #modal-obsolete-cancel').on('click', function () {
        $('#modal-obsolete').addClass('hidden');
    });

    // Close on backdrop click
    $('#modal-obsolete').on('click', function (e) {
        if ($(e.target).is('#modal-obsolete')) {
            $('#modal-obsolete').addClass('hidden');
        }
    });

    // Disable submit button on form submit
    $('#form-obsolete').on('submit', function () {
        $('#btn-obsolete-submit').prop('disabled', true)
            .html('<i class="ti ti-loader-2 animate-spin"></i> Memproses...');
    });
});
</script>
@endpush
