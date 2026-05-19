@extends('layouts.app')

@section('title', 'Daftar Dokumen — SIMARS-DOC')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Dokumen</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola semua dokumen yang dimiliki sistem.</p>
        </div>
        @if (in_array(auth()->user()->role->name, ['super_admin', 'admin_unit']))
            <a href="{{ route('admin.documents.create') }}" class="ina-button ina-button--primary ina-button--sm flex items-center gap-2">
                <i class="ti ti-plus text-sm"></i> Upload Dokumen
            </a>
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

    {{-- Status tabs --}}
    <div class="flex items-center gap-1 mb-4 border-b border-gray-200">
        @php
            $activeStatus = request('status', '');
            $tabs = [
                ''         => ['label' => 'Semua',   'count' => $counts['all']],
                'draft'    => ['label' => 'Draft',   'count' => $counts['draft']],
                'active'   => ['label' => 'Aktif',   'count' => $counts['active']],
                'obsolete' => ['label' => 'Obsolet', 'count' => $counts['obsolete']],
            ];
        @endphp

        @foreach ($tabs as $value => $tab)
            @php
                $isActive = $activeStatus === $value;
                $href     = request()->fullUrlWithQuery(['status' => $value, 'page' => null]);
            @endphp
            <a href="{{ $href }}"
                class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors
                    {{ $isActive
                        ? 'border-blue-600 text-blue-700'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $tab['label'] }}
                <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                    {{ $isActive ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.documents.index') }}" class="flex flex-wrap items-end gap-3 mb-5">
        @if (request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        {{-- Search --}}
        <div class="flex-1 min-w-[200px] ina-text-field">
            <div class="ina-text-field__wrapper">
                <span class="ina-text-field__icon-left"><i class="ti ti-search text-gray-400"></i></span>
                <input type="text" name="q" class="ina-text-field__input pl-8"
                    placeholder="Cari nomor atau judul..."
                    value="{{ request('q') }}">
            </div>
        </div>

        {{-- Document type --}}
        <div class="ina-text-field w-48">
            <div class="ina-text-field__wrapper">
                <select name="type" class="ina-text-field__input">
                    <option value="">Semua Jenis</option>
                    @foreach ($documentTypes as $type)
                        <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                            {{ $type->code }} — {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Unit (super_admin / auditor only) --}}
        @if ($user->role->name !== 'admin_unit')
            <div class="ina-text-field w-48">
                <div class="ina-text-field__wrapper">
                    <select name="unit" class="ina-text-field__input">
                        <option value="">Semua Unit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->code }} — {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <button type="submit" class="ina-button ina-button--secondary ina-button--sm">
            <i class="ti ti-filter text-sm"></i> Filter
        </button>

        @if (request()->hasAny(['q', 'type', 'unit']))
            <a href="{{ route('admin.documents.index', request()->only('status')) }}"
                class="ina-button ina-button--secondary ina-button--sm text-red-500 hover:text-red-700">
                <i class="ti ti-x text-sm"></i> Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if ($documents->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="ti ti-files-off text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm font-medium text-gray-500">Tidak ada dokumen ditemukan.</p>
                <p class="text-xs text-gray-400 mt-1">Coba ubah filter atau
                    @if (in_array(auth()->user()->role->name, ['super_admin', 'admin_unit']))
                        <a href="{{ route('admin.documents.create') }}" class="text-blue-600 hover:underline">upload dokumen baru</a>.
                    @else
                        hubungi admin unit.
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-8">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nomor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Judul</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Jenis</th>
                            @if ($user->role->name !== 'admin_unit')
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Unit</th>
                            @endif
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tgl. Berlaku</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Diupload</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($documents as $index => $doc)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Row number --}}
                                <td class="px-4 py-3 text-gray-400 text-xs">
                                    {{ $documents->firstItem() + $loop->index }}
                                </td>

                                {{-- Number --}}
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs text-gray-600">{{ $doc->number }}</span>
                                </td>

                                {{-- Title --}}
                                <td class="px-4 py-3 max-w-xs">
                                    <a href="{{ route('admin.documents.show', $doc) }}"
                                        class="font-medium text-gray-900 hover:text-blue-700 line-clamp-2 leading-snug">
                                        {{ $doc->title }}
                                    </a>
                                </td>

                                {{-- Type --}}
                                <td class="px-4 py-3">
                                    <span class="text-xs text-gray-500">{{ $doc->documentType?->code }}</span>
                                </td>

                                {{-- Unit (hidden for admin_unit) --}}
                                @if ($user->role->name !== 'admin_unit')
                                    <td class="px-4 py-3">
                                        <span class="text-xs text-gray-500">{{ $doc->ownerUnit?->code }}</span>
                                    </td>
                                @endif

                                {{-- Status badge --}}
                                <td class="px-4 py-3">
                                    @if ($doc->status === 'active')
                                        <span class="ina-badge ina-badge--positive ina-badge--sm">Aktif</span>
                                    @elseif ($doc->status === 'draft')
                                        <span class="ina-badge ina-badge--warning ina-badge--sm">Draft</span>
                                    @else
                                        <span class="ina-badge ina-badge--destructive ina-badge--sm">Obsolet</span>
                                    @endif
                                </td>

                                {{-- Effective date --}}
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $doc->effective_date ? $doc->effective_date->format('d/m/Y') : '—' }}
                                </td>

                                {{-- Uploader --}}
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    <div>{{ $doc->uploader?->name }}</div>
                                    <div class="text-gray-400">{{ $doc->created_at->format('d/m/Y') }}</div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- View --}}
                                        <a href="{{ route('admin.documents.show', $doc) }}"
                                            title="Lihat Detail"
                                            class="ina-button ina-button--secondary ina-button--sm !px-2">
                                            <i class="ti ti-eye text-sm"></i>
                                        </a>

                                        {{-- Edit (draft only, policy-gated) --}}
                                        @if ($doc->status === 'draft' && in_array($user->role->name, ['super_admin', 'admin_unit']))
                                            @can('update', $doc)
                                                <a href="{{ route('admin.documents.edit', $doc) }}"
                                                    title="Edit"
                                                    class="ina-button ina-button--secondary ina-button--sm !px-2">
                                                    <i class="ti ti-pencil text-sm"></i>
                                                </a>
                                            @endcan
                                        @endif

                                        {{-- Publish placeholder (F04) --}}
                                        @if ($doc->status === 'draft' && in_array($user->role->name, ['super_admin', 'admin_unit']))
                                            @can('update', $doc)
                                                <button type="button" disabled
                                                    title="Publikasikan (segera hadir)"
                                                    class="ina-button ina-button--sm !px-2 opacity-40 cursor-not-allowed bg-green-50 text-green-600 border border-green-200">
                                                    <i class="ti ti-send text-sm"></i>
                                                </button>
                                            @endcan
                                        @endif

                                        {{-- Set Obsolete placeholder (F05) --}}
                                        @if ($doc->status === 'active' && in_array($user->role->name, ['super_admin', 'admin_unit']))
                                            @can('update', $doc)
                                                <button type="button" disabled
                                                    title="Set Obsolet (segera hadir)"
                                                    class="ina-button ina-button--sm !px-2 opacity-40 cursor-not-allowed bg-orange-50 text-orange-600 border border-orange-200">
                                                    <i class="ti ti-archive text-sm"></i>
                                                </button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($documents->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Menampilkan {{ $documents->firstItem() }}–{{ $documents->lastItem() }}
                        dari {{ $documents->total() }} dokumen
                    </p>
                    <div class="flex items-center gap-1">
                        {{-- Prev --}}
                        @if ($documents->onFirstPage())
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 cursor-not-allowed !px-2">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </span>
                        @else
                            <a href="{{ $documents->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </a>
                        @endif

                        {{-- Page numbers --}}
                        @foreach ($documents->getUrlRange(max(1, $documents->currentPage() - 2), min($documents->lastPage(), $documents->currentPage() + 2)) as $page => $url)
                            @if ($page === $documents->currentPage())
                                <span class="ina-button ina-button--primary ina-button--sm !px-3 !min-w-[32px]">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="ina-button ina-button--secondary ina-button--sm !px-3 !min-w-[32px]">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if ($documents->hasMorePages())
                            <a href="{{ $documents->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-right text-sm"></i>
                            </a>
                        @else
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 cursor-not-allowed !px-2">
                                <i class="ti ti-chevron-right text-sm"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @else
                <div class="px-4 py-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500">{{ $documents->total() }} dokumen</p>
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Auto-submit filter form on select change
    $('select[name="type"], select[name="unit"]').on('change', function () {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
