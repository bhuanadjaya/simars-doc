@extends('layouts.app')

@section('title', 'Portal Dokumen — SIMARS-DOC')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Portal Dokumen</h1>
            <p class="text-sm text-gray-500 mt-0.5">Cari dan unduh dokumen rumah sakit.</p>
        </div>
        <a href="{{ route('portal.documents.export-excel', request()->only('q','type','unit','year','status')) }}"
            class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1.5">
            <i class="ti ti-file-spreadsheet text-sm"></i>
            <span class="hidden sm:inline">Export Excel</span>
        </a>
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('portal.documents.index') }}" id="filter-form">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">

            {{-- Main search input --}}
            <div class="flex gap-3 mb-3">
                <div class="flex-1 ina-text-field">
                    <div class="ina-text-field__wrapper">
                        <span class="ina-text-field__icon-left"><i class="ti ti-search text-gray-400 text-lg"></i></span>
                        <input type="text" name="q"
                            class="ina-text-field__input pl-9 text-base"
                            placeholder="Cari nomor dokumen, judul, atau kata kunci..."
                            value="{{ request('q') }}"
                            autocomplete="off">
                        @if (request('q'))
                            <button type="button" id="btn-clear-search"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="ti ti-x text-sm"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <button type="submit" class="ina-button ina-button--primary ina-button--md px-6">Cari</button>
            </div>

            {{-- Secondary filters --}}
            <div class="flex flex-wrap gap-3 items-end">

                {{-- Status --}}
                <div class="ina-text-field w-36">
                    <label class="ina-text-field__label text-xs">Status</label>
                    <div class="ina-text-field__wrapper">
                        <select name="status" class="ina-text-field__input text-sm filter-select">
                            <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="obsolete" {{ request('status') === 'obsolete' ? 'selected' : '' }}>Obsolete</option>
                            <option value="" {{ request('status') === '' ? 'selected' : '' }}>Semua</option>
                        </select>
                    </div>
                </div>

                {{-- Document type --}}
                <div class="ina-text-field w-48">
                    <label class="ina-text-field__label text-xs">Jenis Dokumen</label>
                    <div class="ina-text-field__wrapper">
                        <select name="type" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Jenis</option>
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->code }} — {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Unit --}}
                <div class="ina-text-field w-44">
                    <label class="ina-text-field__label text-xs">Unit</label>
                    <div class="ina-text-field__wrapper">
                        <select name="unit" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->code }} — {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Year --}}
                @if ($years->isNotEmpty())
                    <div class="ina-text-field w-32">
                        <label class="ina-text-field__label text-xs">Tahun Berlaku</label>
                        <div class="ina-text-field__wrapper">
                            <select name="year" class="ina-text-field__input text-sm filter-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                {{-- Sort --}}
                <div class="ina-text-field w-44">
                    <label class="ina-text-field__label text-xs">Urutan</label>
                    <div class="ina-text-field__wrapper">
                        <select name="sort" class="ina-text-field__input text-sm filter-select">
                            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Judul A–Z</option>
                            <option value="type" {{ request('sort') === 'type' ? 'selected' : '' }}>Jenis Dokumen</option>
                        </select>
                    </div>
                </div>

                @if (request()->hasAny(['q', 'type', 'unit', 'year', 'sort']) || request('status') !== 'active')
                    <a href="{{ route('portal.documents.index') }}"
                        class="ina-button ina-button--secondary ina-button--sm text-red-500 hover:text-red-700 self-end">
                        <i class="ti ti-x text-sm"></i> Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Results header --}}
    <div class="flex items-center justify-between mb-3">
        <p class="text-sm text-gray-500">
            @if ($documents->total() > 0)
                Menampilkan <span class="font-medium text-gray-700">{{ $documents->firstItem() }}–{{ $documents->lastItem() }}</span>
                dari <span class="font-medium text-gray-700">{{ $documents->total() }}</span> dokumen
                @if (request('q'))
                    untuk <span class="font-medium text-gray-700">"{{ request('q') }}"</span>
                @endif
            @else
                Tidak ada dokumen ditemukan
            @endif
        </p>
    </div>

    {{-- Results --}}
    @if ($documents->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="ti ti-search-off text-5xl text-gray-200 mb-4"></i>
                <p class="text-base font-medium text-gray-500">Dokumen tidak ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba gunakan kata kunci lain atau hapus filter yang aktif.</p>
                <a href="{{ route('portal.documents.index') }}" class="ina-button ina-button--secondary ina-button--sm mt-4">
                    Tampilkan Semua Dokumen
                </a>
            </div>
        </div>
    @else
        <div class="space-y-2">
            @foreach ($documents as $doc)
                @php $isRestricted = $doc->visibility === 'restricted' && $doc->uploaded_by !== $user->id; @endphp
                <div class="bg-white border {{ $isRestricted ? 'border-amber-200' : 'border-gray-200 hover:border-blue-300 hover:shadow-md' }} rounded-xl shadow-sm transition-all">
                    <div class="flex items-start gap-4 p-4">

                        {{-- Icon --}}
                        <div class="shrink-0 mt-0.5">
                            <div class="w-10 h-10 {{ $isRestricted ? 'bg-amber-50 border-amber-100' : 'bg-red-50 border-red-100' }} border rounded-lg flex items-center justify-center">
                                <i class="ti {{ $isRestricted ? 'ti-lock text-amber-500' : 'ti-file-type-pdf text-red-500' }} text-xl"></i>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center flex-wrap gap-2 mb-1">
                                        <span class="text-xs font-mono text-gray-400">{{ $doc->number }}</span>
                                        <span class="ina-badge ina-badge--soft ina-badge--info ina-badge--sm ina-badge--rounded-full">{{ $doc->documentType?->code }}</span>
                                        @if ($doc->status === 'active')
                                            <span class="ina-badge ina-badge--soft ina-badge--success ina-badge--sm ina-badge--rounded-full">Aktif</span>
                                        @else
                                            <span class="ina-badge ina-badge--soft ina-badge--error ina-badge--sm ina-badge--rounded-full">Obsolete</span>
                                        @endif
                                        @if ($isRestricted)
                                            <span class="ina-badge ina-badge--sm" style="background:#fef3c7;color:#92400e;">
                                                <i class="ti ti-lock text-xs mr-0.5"></i> Terbatas
                                            </span>
                                        @endif
                                    </div>

                                    @if ($isRestricted)
                                        <p class="text-base font-semibold text-gray-400 leading-snug line-clamp-2">{{ $doc->title }}</p>
                                        <p class="text-xs text-amber-600 mt-1">Dokumen ini bersifat terbatas dan tidak dapat diakses.</p>
                                    @else
                                        <a href="{{ route('portal.documents.show', $doc) }}"
                                            class="text-base font-semibold text-gray-900 hover:text-blue-700 leading-snug line-clamp-2 block">
                                            {{ $doc->title }}
                                        </a>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <i class="ti ti-building-hospital"></i>
                                            {{ $doc->ownerUnit?->name }}
                                        </span>
                                        @if ($doc->effective_date)
                                            <span class="flex items-center gap-1">
                                                <i class="ti ti-calendar"></i>
                                                Berlaku: {{ $doc->effective_date->format('d/m/Y') }}
                                            </span>
                                        @endif
                                        @if ($doc->expired_at && ! $isRestricted)
                                            <span class="flex items-center gap-1 {{ $doc->expired_at->isPast() ? 'text-red-400' : 'text-orange-400' }}">
                                                <i class="ti ti-clock"></i>
                                                s/d {{ $doc->expired_at->format('d/m/Y') }}
                                                @if ($doc->expired_at->isPast()) (Kadaluarsa) @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="shrink-0 flex items-center gap-2">
                                    @if (! $isRestricted)
                                        <a href="{{ route('portal.documents.show', $doc) }}"
                                            class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1.5">
                                            <i class="ti ti-eye text-sm"></i>
                                            <span class="hidden sm:inline">Lihat</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($documents->hasPages())
            <div class="flex items-center justify-between mt-5">
                <p class="text-xs text-gray-500">Halaman {{ $documents->currentPage() }} dari {{ $documents->lastPage() }}</p>
                <div class="flex items-center gap-1">
                    @if ($documents->onFirstPage())
                        <span class="ina-button ina-button--secondary ina-button--sm opacity-40 cursor-not-allowed !px-2"><i class="ti ti-chevron-left text-sm"></i></span>
                    @else
                        <a href="{{ $documents->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2"><i class="ti ti-chevron-left text-sm"></i></a>
                    @endif
                    @foreach ($documents->getUrlRange(max(1, $documents->currentPage() - 2), min($documents->lastPage(), $documents->currentPage() + 2)) as $page => $url)
                        @if ($page === $documents->currentPage())
                            <span class="ina-button ina-button--primary ina-button--sm !px-3 !min-w-[32px]">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="ina-button ina-button--secondary ina-button--sm !px-3 !min-w-[32px]">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($documents->hasMorePages())
                        <a href="{{ $documents->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2"><i class="ti ti-chevron-right text-sm"></i></a>
                    @else
                        <span class="ina-button ina-button--secondary ina-button--sm opacity-40 cursor-not-allowed !px-2"><i class="ti ti-chevron-right text-sm"></i></span>
                    @endif
                </div>
            </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('.filter-select').on('change', function () { $('#filter-form').submit(); });
    $('#btn-clear-search').on('click', function () {
        $('input[name="q"]').val('');
        $('#filter-form').submit();
    });
});
</script>
@endpush
