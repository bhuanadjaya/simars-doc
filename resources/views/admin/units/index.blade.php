@extends('layouts.app')

@section('title', 'Manajemen Unit — SIMARS-DOC')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Unit</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola struktur unit organisasi.</p>
        </div>
        <a href="{{ route('admin.units.create') }}"
            class="ina-button ina-button--primary ina-button--md flex items-center gap-2">
            <i class="ti ti-plus text-sm"></i>
            Tambah Unit
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.units.index') }}" id="filter-form">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px] ina-text-field">
                    <div class="ina-text-field__wrapper">
                        <span class="ina-text-field__icon-left"><i class="ti ti-search text-gray-400 text-lg"></i></span>
                        <input type="text" name="q" class="ina-text-field__input pl-9 text-sm"
                            placeholder="Cari kode atau nama unit..."
                            value="{{ request('q') }}" autocomplete="off">
                    </div>
                </div>
                <div class="ina-text-field w-36">
                    <label class="ina-text-field__label text-xs">Status</label>
                    <div class="ina-text-field__wrapper">
                        <select name="status" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="ina-button ina-button--primary ina-button--md px-5">Cari</button>
                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.units.index') }}"
                        class="ina-button ina-button--secondary ina-button--md text-red-500">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if ($units->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="ti ti-building-hospital text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500">Belum ada unit terdaftar</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit Induk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($units as $unit)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-mono font-medium text-gray-700">{{ $unit->code }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $unit->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $unit->parent?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $unit->users_count }}</td>
                            <td class="px-4 py-3">
                                @if ($unit->is_active)
                                    <span class="ina-badge ina-badge--positive ina-badge--sm">Aktif</span>
                                @else
                                    <span class="ina-badge ina-badge--neutral ina-badge--sm">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 justify-end">
                                    @if ($unit->is_active)
                                        <form method="POST" action="{{ route('admin.units.deactivate', $unit) }}"
                                            class="form-deactivate inline">
                                            @csrf
                                            <button type="submit"
                                                class="ina-button ina-button--danger ina-button--sm"
                                                title="Nonaktifkan">
                                                <i class="ti ti-power text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.units.activate', $unit) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="ina-button ina-button--secondary ina-button--sm text-green-600"
                                                title="Aktifkan kembali">
                                                <i class="ti ti-power text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($units->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        {{ $units->firstItem() }}–{{ $units->lastItem() }} dari {{ $units->total() }} unit
                    </p>
                    <div class="flex gap-1">
                        @if ($units->onFirstPage())
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 !px-2 cursor-not-allowed">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </span>
                        @else
                            <a href="{{ $units->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </a>
                        @endif
                        @if ($units->hasMorePages())
                            <a href="{{ $units->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
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

    $('.form-deactivate').on('submit', function (e) {
        e.preventDefault();
        if (confirm('Nonaktifkan unit ini? Unit yang memiliki dokumen aktif tidak dapat dinonaktifkan.')) {
            this.submit();
        }
    });
});
</script>
@endpush
