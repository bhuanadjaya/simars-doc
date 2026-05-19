@extends('layouts.app')

@section('title', $document->title . ' — SIMARS-DOC')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Back link --}}
    <div class="mb-4">
        <a href="{{ route('portal.documents.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition-colors">
            <i class="ti ti-arrow-left text-sm"></i>
            Kembali ke daftar dokumen
        </a>
    </div>

    {{-- Replaced-by banner --}}
    @if ($document->replacedBy)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4 flex items-start gap-3">
            <i class="ti ti-alert-triangle text-amber-500 text-xl mt-0.5 shrink-0"></i>
            <div>
                <p class="text-sm font-medium text-amber-800">Dokumen ini telah digantikan</p>
                <p class="text-sm text-amber-700 mt-0.5">
                    Digantikan oleh:
                    <a href="{{ route('portal.documents.show', $document->replacedBy) }}"
                        class="font-semibold underline hover:text-amber-900">
                        {{ $document->replacedBy->title }}
                    </a>
                </p>
            </div>
        </div>
    @endif

    {{-- Replaces info --}}
    @if ($document->replaces)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 flex items-start gap-3">
            <i class="ti ti-info-circle text-blue-500 text-xl mt-0.5 shrink-0"></i>
            <div>
                <p class="text-sm font-medium text-blue-800">Dokumen ini menggantikan</p>
                <p class="text-sm text-blue-700 mt-0.5">
                    Menggantikan:
                    <span class="font-semibold">{{ $document->replaces->title }}</span>
                    ({{ $document->replaces->number }})
                </p>
            </div>
        </div>
    @endif

    {{-- Parent document info --}}
    @if ($document->parentDocument)
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 mb-4 flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-history text-gray-400"></i>
            Versi sebelumnya:
            <a href="{{ route('portal.documents.show', $document->parentDocument) }}"
                class="font-medium text-blue-600 hover:underline">
                {{ $document->parentDocument->title }}
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: metadata --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Document info card --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 bg-red-50 border border-red-100 rounded-lg flex items-center justify-center shrink-0">
                        <i class="ti ti-file-type-pdf text-red-500 text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="ina-badge ina-badge--info ina-badge--sm">{{ $document->documentType?->code }}</span>
                        <span class="ina-badge ina-badge--positive ina-badge--sm ml-1">Aktif</span>
                    </div>
                </div>

                <h1 class="text-base font-bold text-gray-900 leading-snug mb-1">{{ $document->title }}</h1>
                <p class="text-xs font-mono text-gray-400 mb-4">{{ $document->number }}</p>

                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Jenis Dokumen</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $document->documentType?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Unit Pemilik</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $document->ownerUnit?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Sumber</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">
                            {{ $document->source === 'internal' ? 'Internal' : 'Eksternal' }}
                        </dd>
                    </div>
                    @if ($document->effective_date)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide">Tanggal Berlaku</dt>
                            <dd class="font-medium text-gray-800 mt-0.5">{{ $document->effective_date->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if ($document->published_at)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide">Dipublikasikan</dt>
                            <dd class="font-medium text-gray-800 mt-0.5">{{ $document->published_at->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Diupload oleh</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $document->uploader?->name ?? '—' }}</dd>
                    </div>
                    @if ($document->tags)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide">Tags</dt>
                            <dd class="font-medium text-gray-800 mt-0.5">{{ $document->tags }}</dd>
                        </div>
                    @endif
                    @if ($document->description)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide">Deskripsi</dt>
                            <dd class="text-gray-700 mt-0.5 leading-relaxed">{{ $document->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Download button --}}
            @if ($canDownload)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                    <p class="text-xs text-gray-500 mb-3 font-medium uppercase tracking-wide">Unduh Dokumen</p>
                    <a href="{{ route('portal.documents.download', $document) }}"
                        class="ina-button ina-button--primary ina-button--md w-full flex items-center justify-center gap-2">
                        <i class="ti ti-download"></i>
                        Unduh PDF
                    </a>
                    @if ($document->docxFile)
                        <p class="text-xs text-gray-400 text-center mt-2">File DOCX tersedia — hubungi admin unit.</p>
                    @endif
                </div>
            @endif

        </div>

        {{-- Right: PDF viewer --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-700 flex items-center gap-2">
                        <i class="ti ti-eye text-gray-400"></i>
                        Pratinjau Dokumen
                    </p>
                    @if ($canDownload)
                        <a href="{{ route('portal.documents.download', $document) }}"
                            class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1.5">
                            <i class="ti ti-download text-sm"></i>
                            Unduh
                        </a>
                    @endif
                </div>

                @if ($document->pdfFile)
                    <div class="bg-gray-100" style="height: 700px;">
                        <iframe
                            src="{{ route('portal.documents.stream', $document) }}"
                            class="w-full h-full border-0"
                            title="{{ $document->title }}"
                        ></iframe>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <i class="ti ti-file-off text-5xl text-gray-200 mb-4"></i>
                        <p class="text-gray-500 text-sm">File PDF tidak tersedia</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
