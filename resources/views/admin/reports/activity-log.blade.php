@extends('layouts.app')

@section('title', 'Log Aktivitas — SIMARS-DOC')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Log Aktivitas</h1>
            <p class="text-sm text-gray-500 mt-0.5">Rekam jejak seluruh aktivitas pengguna.</p>
        </div>
        <a href="{{ route('admin.reports.export-activity-log', request()->query()) }}"
            class="ina-button ina-button--secondary ina-button--md flex items-center gap-2">
            <i class="ti ti-table-export text-sm"></i>
            Export Excel
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.reports.activity-log') }}" id="filter-form">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="ina-text-field w-52">
                    <label class="ina-text-field__label text-xs">Pengguna</label>
                    <div class="ina-text-field__wrapper">
                        <select name="user" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Pengguna</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('user') === $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ina-text-field w-44">
                    <label class="ina-text-field__label text-xs">Aksi</label>
                    <div class="ina-text-field__wrapper">
                        <select name="action" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Aksi</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ina-text-field w-40">
                    <label class="ina-text-field__label text-xs">Dari Tanggal</label>
                    <div class="ina-text-field__wrapper">
                        <input type="date" name="date_from" class="ina-text-field__input text-sm"
                            value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="ina-text-field w-40">
                    <label class="ina-text-field__label text-xs">Sampai Tanggal</label>
                    <div class="ina-text-field__wrapper">
                        <input type="date" name="date_to" class="ina-text-field__input text-sm"
                            value="{{ request('date_to') }}">
                    </div>
                </div>
                <button type="submit" class="ina-button ina-button--primary ina-button--md px-5">Filter</button>
                @if (request()->hasAny(['user', 'action', 'date_from', 'date_to']))
                    <a href="{{ route('admin.reports.activity-log') }}"
                        class="ina-button ina-button--secondary ina-button--md text-red-500">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if ($logs->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="ti ti-activity text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500">Tidak ada log ditemukan</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dokumen</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-2.5 font-medium text-gray-800">{{ $log->user?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5">
                                <span class="ina-badge ina-badge--info ina-badge--sm">{{ str_replace('_', ' ', $log->action) }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">
                                @if ($log->document)
                                    <a href="{{ route('admin.documents.show', $log->document) }}"
                                        class="hover:text-blue-700 truncate block max-w-xs">
                                        {{ $log->document->title }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-gray-400 font-mono text-xs">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} entri
                    </p>
                    <div class="flex gap-1">
                        @if ($logs->onFirstPage())
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 !px-2 cursor-not-allowed">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </span>
                        @else
                            <a href="{{ $logs->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </a>
                        @endif
                        @if ($logs->hasMorePages())
                            <a href="{{ $logs->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
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
