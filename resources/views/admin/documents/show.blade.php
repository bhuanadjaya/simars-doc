@extends('layouts.app')

@section('title', $document->title . ' — SIMARS-DOC')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-start justify-between mb-6">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.documents.index') }}" class="ina-button ina-button--secondary ina-button--sm mt-0.5 flex items-center gap-1">
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
                @can('revertToDraft', $document)
                    <button type="button" id="btn-revert"
                        class="ina-button ina-button--sm flex items-center gap-1.5 bg-yellow-50 text-yellow-700 border border-yellow-300 hover:bg-yellow-100">
                        <i class="ti ti-arrow-back-up text-sm"></i> Kembalikan ke Draft
                    </button>
                    <form id="form-revert" method="POST"
                        action="{{ route('admin.documents.revert-to-draft', $document) }}" class="hidden">
                        @csrf @method('PATCH')
                    </form>
                @endcan

                @can('obsolete', $document)
                    <button type="button" id="btn-obsolete"
                        class="ina-button ina-button--sm flex items-center gap-1.5 bg-orange-50 text-orange-700 border border-orange-300 hover:bg-orange-100">
                        <i class="ti ti-archive text-sm"></i> Set Obsolet
                    </button>
                @endcan
            @endif

            @if (in_array($document->status, ['active', 'obsolete']))
                @if (! $document->is_reviewed)
                    @can('review', $document)
                        <button type="button" id="btn-review"
                            class="ina-button ina-button--sm flex items-center gap-1.5 bg-purple-50 text-purple-700 border border-purple-300 hover:bg-purple-100">
                            <i class="ti ti-clipboard-check text-sm"></i> Review
                        </button>
                    @endcan
                @else
                    @can('unreview', $document)
                        <button type="button" id="btn-unreview"
                            class="ina-button ina-button--sm flex items-center gap-1.5 bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200">
                            <i class="ti ti-clipboard-x text-sm"></i> Batalkan Review
                        </button>
                        <form id="form-unreview" method="POST"
                            action="{{ route('admin.documents.unreview', $document) }}" class="hidden">
                            @csrf
                        </form>
                    @endcan
                @endif
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
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Masa Berlaku s/d</dt>
                <dd class="mt-0.5 flex items-center gap-2">
                    @if ($document->expired_at)
                        <span class="font-medium text-gray-900">{{ $document->expired_at->format('d/m/Y') }}</span>
                        @if ($document->expired_at->isPast())
                            <span class="ina-badge ina-badge--destructive ina-badge--sm">Kadaluarsa</span>
                        @elseif ($document->reminder_months && now()->greaterThanOrEqualTo($document->expired_at->subMonths($document->reminder_months)))
                            <span class="ina-badge ina-badge--warning ina-badge--sm">Segera Expired</span>
                        @endif
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </dd>
            </div>
            @if ($document->reminder_months)
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Reminder Expired</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $document->reminder_months }} bulan sebelum expired</dd>
            </div>
            @endif
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Visibilitas</dt>
                <dd class="mt-0.5">
                    @if ($document->visibility === 'restricted')
                        <span class="ina-badge ina-badge--warning ina-badge--sm flex items-center gap-1 w-fit">
                            <i class="ti ti-lock text-xs"></i> Terbatas
                        </span>
                    @else
                        <span class="ina-badge ina-badge--neutral ina-badge--sm">Publik</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs uppercase tracking-wide">Status Review</dt>
                <dd class="mt-0.5">
                    @if ($document->is_reviewed)
                        <span class="ina-badge ina-badge--positive ina-badge--sm flex items-center gap-1 w-fit">
                            <i class="ti ti-clipboard-check text-xs"></i> Sudah Direview
                        </span>
                        @if ($document->reviewer)
                            <p class="text-xs text-gray-400 mt-1">oleh {{ $document->reviewer->name }} · {{ $document->reviewed_at?->format('d/m/Y H:i') }}</p>
                        @endif
                        @if ($document->review_notes)
                            <p class="text-xs text-gray-500 mt-1 italic">"{{ $document->review_notes }}"</p>
                        @endif
                    @else
                        <span class="ina-badge ina-badge--neutral ina-badge--sm">Belum Direview</span>
                    @endif
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
        @if ($document->parentDocument || $document->replacedBy)
            <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                @if ($document->parentDocument)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Merevisi Dokumen</p>
                        <a href="{{ route('admin.documents.show', $document->parentDocument) }}"
                            class="inline-flex items-center gap-1.5 text-sm text-blue-700 hover:text-blue-900 hover:underline">
                            <i class="ti ti-arrow-back-up"></i>
                            {{ $document->parentDocument->number }} — {{ $document->parentDocument->title }}
                        </a>
                    </div>
                @endif
                @if ($document->replacedBy)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Digantikan Oleh</p>
                        <a href="{{ route('admin.documents.show', $document->replacedBy) }}"
                            class="inline-flex items-center gap-1.5 text-sm text-orange-700 hover:text-orange-900 hover:underline">
                            <i class="ti ti-arrow-forward-up"></i>
                            {{ $document->replacedBy->number }} — {{ $document->replacedBy->title }}
                        </a>
                    </div>
                @endif
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
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50
                        {{ $file->file_type === 'pdf' ? 'hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-colors' : '' }}"
                        @if ($file->file_type === 'pdf')
                            onclick="openPreview('{{ route('admin.documents.stream', $document) }}')"
                        @endif>
                        <div class="flex items-center gap-3">
                            <i class="ti {{ $file->file_type === 'pdf' ? 'ti-file-type-pdf text-red-500' : 'ti-file-type-docx text-blue-500' }} text-2xl"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $file->original_filename }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ strtoupper($file->file_type) }} &middot;
                                    {{ number_format($file->file_size / 1024 / 1024, 2) }} MB
                                    @if ($file->file_type === 'pdf')
                                        &middot; <span class="text-blue-500">klik untuk pratinjau</span>
                                    @else
                                        &middot; <span class="text-gray-400">pratinjau tidak tersedia</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($file->file_type === 'pdf')
                                <span class="ina-badge ina-badge--info ina-badge--sm uppercase">PDF</span>
                                <i class="ti ti-eye text-gray-400 text-base"></i>
                            @else
                                <a href="{{ route('admin.documents.download', [$document, 'type' => 'docx']) }}"
                                    onclick="event.stopPropagation()"
                                    class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1">
                                    <i class="ti ti-download text-sm"></i> Unduh DOCX
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

{{-- PDF Preview Modal --}}
<div id="modal-preview" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl flex flex-col w-full max-w-5xl" style="height: 90vh;">
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 shrink-0">
            <p class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-file-type-pdf text-red-500 text-lg"></i>
                Pratinjau Dokumen — {{ $document->title }}
            </p>
            <button type="button" id="modal-preview-close"
                class="text-gray-400 hover:text-gray-700 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="ti ti-x text-lg"></i>
            </button>
        </div>
        <div class="flex-1 bg-gray-100 min-h-0">
            <iframe id="preview-iframe" src="" class="w-full h-full border-0" title="Pratinjau PDF"></iframe>
        </div>
    </div>
</div>

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
                            <label class="ina-text-field__label">
                                Digantikan Oleh <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>

                            @if ($document->replacedBy)
                                {{-- Auto-link sudah terjadi saat publish — tampilkan read-only --}}
                                <input type="hidden" name="replaced_by_id" value="{{ $document->replaced_by_id }}">
                                <div class="ina-text-field__wrapper">
                                    <input type="text" class="ina-text-field__input bg-gray-50 text-gray-600 cursor-not-allowed"
                                        value="{{ $document->replacedBy->number }} — {{ $document->replacedBy->title }}"
                                        disabled>
                                </div>
                                <p class="text-xs text-blue-500 mt-1 flex items-center gap-1">
                                    <i class="ti ti-link text-xs"></i>
                                    Terisi otomatis saat dokumen pengganti dipublikasikan.
                                </p>
                            @else
                                {{-- Belum ada pengganti — tampilkan dropdown --}}
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
                            @endif
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

{{-- Review modal --}}
@if (in_array($document->status, ['active', 'obsolete']) && ! $document->is_reviewed)
    @can('review', $document)
        <div id="modal-review" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Review Dokumen</h3>
                    <button type="button" id="modal-review-close" class="text-gray-400 hover:text-gray-600">
                        <i class="ti ti-x text-lg"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.documents.review', $document) }}" id="form-review">
                    @csrf
                    <div class="px-6 py-5 space-y-4">
                        <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg text-sm text-purple-700">
                            <p class="font-medium">{{ $document->number }} — {{ $document->title }}</p>
                        </div>
                        <div class="ina-text-field">
                            <label class="ina-text-field__label" for="reviewed_at">
                                Tanggal Review <span class="text-red-500">*</span>
                            </label>
                            <div class="ina-text-field__wrapper">
                                <input type="date" id="reviewed_at" name="reviewed_at"
                                    class="ina-text-field__input"
                                    value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Kadaluarsa akan otomatis diset 2 tahun setelah tanggal ini.</p>
                        </div>
                        <div class="ina-text-field">
                            <label class="ina-text-field__label" for="review_notes">
                                Catatan Review <span class="text-red-500">*</span>
                            </label>
                            <div class="ina-text-field__wrapper">
                                <textarea id="review_notes" name="review_notes"
                                    class="ina-text-field__input min-h-[100px] resize-y"
                                    placeholder="Tuliskan catatan hasil review dokumen ini..."
                                    required maxlength="2000"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                        <button type="button" id="modal-review-cancel"
                            class="ina-button ina-button--secondary ina-button--sm">Batal</button>
                        <button type="submit" id="btn-review-submit"
                            class="ina-button ina-button--sm flex items-center gap-1.5 bg-purple-600 text-white border border-purple-700 hover:bg-purple-700">
                            <i class="ti ti-clipboard-check text-sm"></i> Simpan Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@endif

@push('scripts')
<script>
function openPreview(streamUrl) {
    $('#preview-iframe').attr('src', streamUrl);
    $('#modal-preview').removeClass('hidden');
}

$(document).ready(function () {
    // Close preview modal
    $('#modal-preview-close').on('click', function () {
        $('#modal-preview').addClass('hidden');
        $('#preview-iframe').attr('src', '');
    });

    $('#modal-preview').on('click', function (e) {
        if ($(e.target).is('#modal-preview')) {
            $('#modal-preview').addClass('hidden');
            $('#preview-iframe').attr('src', '');
        }
    });

    // Publish confirm
    $('#btn-publish').on('click', function () {
        if (confirm('Publikasikan dokumen ini? Status akan berubah menjadi Aktif dan tidak dapat dikembalikan ke Draft.')) {
            $(this).prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i> Memproses...');
            $('#form-publish').submit();
        }
    });

    // Revert to draft confirm
    $('#btn-revert').on('click', function () {
        if (confirm('Kembalikan dokumen ini ke Draft? Status aktif akan dicabut dan dokumen tidak dapat diakses portal hingga dipublikasikan kembali.')) {
            $(this).prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i> Memproses...');
            $('#form-revert').submit();
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

    // Review modal
    $('#btn-review').on('click', function () {
        $('#modal-review').removeClass('hidden');
        $('#review_notes').focus();
    });
    $('#modal-review-close, #modal-review-cancel').on('click', function () {
        $('#modal-review').addClass('hidden');
    });
    $('#modal-review').on('click', function (e) {
        if ($(e.target).is('#modal-review')) $('#modal-review').addClass('hidden');
    });
    $('#form-review').on('submit', function () {
        $('#btn-review-submit').prop('disabled', true)
            .html('<i class="ti ti-loader-2 animate-spin"></i> Menyimpan...');
    });

    // Unreview confirm
    $('#btn-unreview').on('click', function () {
        if (confirm('Batalkan review dokumen ini? Data review akan dihapus.')) {
            $(this).prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin"></i>');
            $('#form-unreview').submit();
        }
    });
});
</script>
@endpush
