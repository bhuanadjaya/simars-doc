@extends('layouts.app')

@section('title', 'Regulasi Eksternal — SIMARS-DOC')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Regulasi Eksternal</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola peraturan dan regulasi dari instansi luar.</p>
        </div>
        <a href="{{ route('admin.external-regulations.create') }}"
            class="ina-button ina-button--primary ina-button--md flex items-center gap-2">
            <i class="ti ti-plus text-sm"></i>
            Tambah Regulasi
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.external-regulations.index') }}" id="filter-form">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px] ina-text-field">
                    <div class="ina-text-field__wrapper">
                        <span class="ina-text-field__icon-left"><i class="ti ti-search text-gray-400 text-lg"></i></span>
                        <input type="text" name="q" class="ina-text-field__input pl-9 text-sm"
                            placeholder="Cari nomor, judul, atau instansi..."
                            value="{{ request('q') }}" autocomplete="off">
                    </div>
                </div>
                <div class="ina-text-field w-52">
                    <label class="ina-text-field__label text-xs">Kategori</label>
                    <div class="ina-text-field__wrapper">
                        <select name="category" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Kategori</option>
                            @foreach (\App\Models\ExternalRegulation::$categoryLabels as $val => $label)
                                <option value="{{ $val }}" {{ request('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ina-text-field w-36">
                    <label class="ina-text-field__label text-xs">Status</label>
                    <div class="ina-text-field__wrapper">
                        <select name="status" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="ina-button ina-button--primary ina-button--md px-5">Cari</button>
                @if (request()->hasAny(['q', 'category', 'status']))
                    <a href="{{ route('admin.external-regulations.index') }}"
                        class="ina-button ina-button--secondary ina-button--md text-red-500">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if ($regulations->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="ti ti-file-x text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500">Belum ada regulasi eksternal</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor / Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Instansi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Berlaku</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($regulations as $index => $reg)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-400">{{ $regulations->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3">
                                <div class="text-xs font-mono text-gray-400">{{ $reg->regulation_number }}</div>
                                <a href="{{ route('admin.external-regulations.show', $reg) }}"
                                    class="text-sm font-medium text-gray-900 hover:text-blue-700 line-clamp-2">
                                    {{ $reg->title }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="ina-badge ina-badge--info ina-badge--sm">{{ $reg->category_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $reg->issuing_agency }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $reg->effective_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                @if ($reg->status === 'active')
                                    <span class="ina-badge ina-badge--positive ina-badge--sm">Aktif</span>
                                @else
                                    <span class="ina-badge ina-badge--neutral ina-badge--sm">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('admin.external-regulations.show', $reg) }}"
                                        class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1">
                                        <i class="ti ti-eye text-sm"></i> Lihat
                                    </a>
                                    <a href="{{ route('admin.external-regulations.edit', $reg) }}"
                                        class="ina-button ina-button--secondary ina-button--sm flex items-center gap-1">
                                        <i class="ti ti-pencil text-sm"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.external-regulations.destroy', $reg) }}"
                                        class="form-delete inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="ina-button ina-button--danger ina-button--sm flex items-center gap-1">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($regulations->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        {{ $regulations->firstItem() }}–{{ $regulations->lastItem() }} dari {{ $regulations->total() }} regulasi
                    </p>
                    <div class="flex gap-1">
                        @if ($regulations->onFirstPage())
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 !px-2 cursor-not-allowed">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </span>
                        @else
                            <a href="{{ $regulations->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </a>
                        @endif
                        @if ($regulations->hasMorePages())
                            <a href="{{ $regulations->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
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

    $('.form-delete').on('submit', function (e) {
        e.preventDefault();
        if (confirm('Hapus regulasi ini? Tindakan ini tidak dapat dibatalkan.')) {
            this.submit();
        }
    });
});
</script>
@endpush
