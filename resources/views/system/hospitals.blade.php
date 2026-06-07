@extends('layouts.app')

@section('title', 'Daftar Rumah Sakit — System Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Rumah Sakit</h1>
    <p class="text-sm text-gray-500 mt-1">Semua rumah sakit yang terdaftar di platform.</p>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('system.hospitals') }}" id="filter-form">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48 ina-text-field">
            <div class="ina-text-field__wrapper">
                <span class="ina-text-field__icon-left"><i class="ti ti-search text-gray-400 text-lg"></i></span>
                <input type="text" name="q" class="ina-text-field__input pl-9"
                    placeholder="Cari nama atau kode RS..." value="{{ request('q') }}" autocomplete="off">
            </div>
        </div>
        <div class="ina-text-field w-36">
            <div class="ina-text-field__wrapper">
                <select name="status" class="ina-text-field__input text-sm filter-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>
        <button type="submit" class="ina-button ina-button--primary ina-button--md">Cari</button>
        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('system.hospitals') }}" class="ina-button ina-button--secondary ina-button--md text-red-500">
                <i class="ti ti-x text-sm"></i> Reset
            </a>
        @endif
    </div>
</form>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
        <p class="text-sm text-gray-500">
            {{ $hospitals->total() }} rumah sakit
        </p>
    </div>

    @if ($hospitals->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center text-gray-400">
            <i class="ti ti-building-hospital text-5xl mb-3"></i>
            <p class="text-sm">Tidak ada rumah sakit ditemukan</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rumah Sakit</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kontak</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Pengguna</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Dokumen</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Terdaftar</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($hospitals as $hospital)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-gray-900">{{ $hospital->name }}</p>
                            <p class="text-xs font-mono text-gray-400 mt-0.5">{{ $hospital->code }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-gray-700">{{ $hospital->email ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $hospital->phone ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="font-semibold text-gray-800">{{ number_format($hospital->users_count) }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="font-semibold text-gray-800">{{ number_format($hospital->documents_count) }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center text-xs text-gray-400">
                            {{ $hospital->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if ($hospital->is_active)
                                <span class="ina-badge ina-badge--positive ina-badge--sm">Aktif</span>
                            @else
                                <span class="ina-badge ina-badge--neutral ina-badge--sm">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($hospitals->hasPages())
            <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100">
                <p class="text-xs text-gray-500">Halaman {{ $hospitals->currentPage() }} dari {{ $hospitals->lastPage() }}</p>
                <div class="flex items-center gap-1">
                    @if ($hospitals->onFirstPage())
                        <span class="ina-button ina-button--secondary ina-button--sm opacity-40 cursor-not-allowed !px-2"><i class="ti ti-chevron-left text-sm"></i></span>
                    @else
                        <a href="{{ $hospitals->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2"><i class="ti ti-chevron-left text-sm"></i></a>
                    @endif
                    @if ($hospitals->hasMorePages())
                        <a href="{{ $hospitals->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2"><i class="ti ti-chevron-right text-sm"></i></a>
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
});
</script>
@endpush
