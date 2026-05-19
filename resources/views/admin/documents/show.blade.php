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

        {{-- Actions (draft only) --}}
        @if ($document->status === 'draft')
            @can('update', $document)
                <a href="{{ route('admin.documents.edit', $document) }}"
                    class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1.5">
                    <i class="ti ti-pencil text-sm"></i> Edit
                </a>
            @endcan
        @endif
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
