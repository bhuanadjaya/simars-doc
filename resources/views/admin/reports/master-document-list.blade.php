@extends('layouts.app')

@section('title', 'Daftar Induk Dokumen — SIMARS-DOC')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Induk Dokumen</h1>
            <p class="text-sm text-gray-500 mt-0.5">Semua dokumen aktif dalam sistem.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.export-excel', request()->query()) }}"
                class="ina-button ina-button--secondary ina-button--md flex items-center gap-2">
                <i class="ti ti-table-export text-sm"></i>
                Export Excel
            </a>
            <a href="{{ route('admin.reports.export-pdf', request()->query()) }}"
                target="_blank"
                class="ina-button ina-button--secondary ina-button--md flex items-center gap-2">
                <i class="ti ti-printer text-sm"></i>
                Cetak PDF
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.reports.master-document-list') }}" id="filter-form">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="ina-text-field w-52">
                    <label class="ina-text-field__label text-xs">Jenis Dokumen</label>
                    <div class="ina-text-field__wrapper">
                        <select name="type" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Jenis</option>
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ request('type') === $type->id ? 'selected' : '' }}>
                                    {{ $type->code }} — {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ina-text-field w-44">
                    <label class="ina-text-field__label text-xs">Unit</label>
                    <div class="ina-text-field__wrapper">
                        <select name="unit" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit') === $unit->id ? 'selected' : '' }}>
                                    {{ $unit->code }} — {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if ($years->isNotEmpty())
                    <div class="ina-text-field w-32">
                        <label class="ina-text-field__label text-xs">Tahun</label>
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
                @if (request()->hasAny(['type', 'unit', 'year']))
                    <a href="{{ route('admin.reports.master-document-list') }}"
                        class="ina-button ina-button--secondary ina-button--md text-red-500">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="text-sm text-gray-500 mb-3">
        {{ $documents->total() }} dokumen aktif ditemukan
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if ($documents->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="ti ti-file-off text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500">Tidak ada dokumen ditemukan</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Berlaku</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Publikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($documents as $doc)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 text-gray-400">{{ $documents->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-2.5 font-mono text-xs text-gray-600">{{ $doc->number }}</td>
                            <td class="px-4 py-2.5">
                                <a href="{{ route('admin.documents.show', $doc) }}"
                                    class="font-medium text-gray-900 hover:text-blue-700">
                                    {{ $doc->title }}
                                </a>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="ina-badge ina-badge--info ina-badge--sm">{{ $doc->documentType?->code }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">{{ $doc->ownerUnit?->code }}</td>
                            <td class="px-4 py-2.5 text-gray-600">{{ $doc->effective_date?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-600">{{ $doc->published_at?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($documents->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Halaman {{ $documents->currentPage() }} dari {{ $documents->lastPage() }}
                    </p>
                    <div class="flex gap-1">
                        @if ($documents->onFirstPage())
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 !px-2 cursor-not-allowed">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </span>
                        @else
                            <a href="{{ $documents->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </a>
                        @endif
                        @if ($documents->hasMorePages())
                            <a href="{{ $documents->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-right text-sm"></i>
                            </a>
                        @else
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 !px-2 cursor-not-allowed">
                                <i class="ti ti-chevron-right text-sm"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('.filter-select').on('change', function () { $('#filter-form').submit(); });
});
</script>
@endpush
