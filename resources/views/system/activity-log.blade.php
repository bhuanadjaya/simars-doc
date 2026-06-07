@extends('layouts.app')

@section('title', 'Log Aktivitas — System Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Log Aktivitas</h1>
    <p class="text-sm text-gray-500 mt-1">Seluruh aktivitas pengguna lintas rumah sakit.</p>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('system.activity-log') }}" id="filter-form">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div class="ina-text-field w-52">
            <label class="ina-text-field__label text-xs">Rumah Sakit</label>
            <div class="ina-text-field__wrapper">
                <select name="hospital" class="ina-text-field__input text-sm filter-select">
                    <option value="">Semua RS</option>
                    @foreach ($hospitals as $h)
                        <option value="{{ $h->id }}" {{ request('hospital') == $h->id ? 'selected' : '' }}>
                            {{ $h->code }} — {{ $h->name }}
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
        <div class="ina-text-field w-36">
            <label class="ina-text-field__label text-xs">Dari Tanggal</label>
            <div class="ina-text-field__wrapper">
                <input type="date" name="date_from" class="ina-text-field__input text-sm" value="{{ request('date_from') }}">
            </div>
        </div>
        <div class="ina-text-field w-36">
            <label class="ina-text-field__label text-xs">Sampai Tanggal</label>
            <div class="ina-text-field__wrapper">
                <input type="date" name="date_to" class="ina-text-field__input text-sm" value="{{ request('date_to') }}">
            </div>
        </div>
        <button type="submit" class="ina-button ina-button--primary ina-button--md self-end">Cari</button>
        @if (request()->hasAny(['hospital', 'action', 'date_from', 'date_to']))
            <a href="{{ route('system.activity-log') }}" class="ina-button ina-button--secondary ina-button--md self-end text-red-500">
                <i class="ti ti-x text-sm"></i> Reset
            </a>
        @endif
    </div>
</form>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100">
        <p class="text-sm text-gray-500">{{ $logs->total() }} entri</p>
    </div>

    @if ($logs->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center text-gray-400">
            <i class="ti ti-list-details text-5xl mb-3"></i>
            <p class="text-sm">Tidak ada log ditemukan</p>
        </div>
    @else
        <div class="divide-y divide-gray-50">
            @foreach ($logs as $log)
                <div class="flex items-start gap-4 px-5 py-3">
                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center shrink-0 mt-0.5">
                        @php
                            $icon = match ($log->action) {
                                'login'              => 'ti-login',
                                'logout'             => 'ti-logout',
                                'create_document'    => 'ti-file-plus',
                                'publish_document'   => 'ti-circle-check',
                                'set_obsolete'       => 'ti-file-x',
                                'delete_document'    => 'ti-trash',
                                'view_document'      => 'ti-eye',
                                'download_document'  => 'ti-download',
                                'review_document'    => 'ti-clipboard-check',
                                default              => 'ti-activity',
                            };
                        @endphp
                        <i class="ti {{ $icon }} text-sm text-gray-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center flex-wrap gap-x-2 gap-y-0.5">
                            <span class="font-medium text-sm text-gray-900">{{ $log->user?->name ?? '—' }}</span>
                            <span class="text-xs text-gray-400">·</span>
                            <span class="text-xs text-gray-600">{{ str_replace('_', ' ', $log->action) }}</span>
                            @if ($log->document)
                                <span class="text-xs text-gray-400">·</span>
                                <span class="text-xs text-gray-500 truncate max-w-xs">{{ $log->document->title }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-0.5">
                            @if ($log->user?->hospital)
                                <span class="text-xs font-mono text-[#2596be] bg-blue-50 px-1.5 py-0.5 rounded">
                                    {{ $log->user->hospital->code }}
                                </span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $log->created_at->format('d M Y, H:i') }}</span>
                            @if ($log->ip_address)
                                <span class="text-xs text-gray-300">{{ $log->ip_address }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($logs->hasPages())
            <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100">
                <p class="text-xs text-gray-500">Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}</p>
                <div class="flex items-center gap-1">
                    @if ($logs->onFirstPage())
                        <span class="ina-button ina-button--secondary ina-button--sm opacity-40 cursor-not-allowed !px-2"><i class="ti ti-chevron-left text-sm"></i></span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2"><i class="ti ti-chevron-left text-sm"></i></a>
                    @endif
                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2"><i class="ti ti-chevron-right text-sm"></i></a>
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
