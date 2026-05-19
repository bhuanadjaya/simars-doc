@extends('layouts.app')

@section('title', 'Statistik Penggunaan — SIMARS-DOC')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Statistik Penggunaan</h1>
        <p class="text-sm text-gray-500 mt-0.5">Data aktivitas 30 hari terakhir.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top downloads --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i class="ti ti-download text-blue-500"></i>
                    Top 10 Dokumen Diunduh
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">30 hari terakhir</p>
            </div>

            @if ($topDownloads->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center text-gray-400">
                    <i class="ti ti-file-off text-3xl mb-2"></i>
                    <p class="text-sm">Belum ada unduhan</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach ($topDownloads as $index => $entry)
                        <div class="flex items-center gap-3 px-5 py-3">
                            <span class="text-lg font-bold text-gray-200 w-7 shrink-0 text-center">{{ $index + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">
                                    {{ $entry->document?->title ?? 'Dokumen dihapus' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $entry->document?->number }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="text-base font-bold text-blue-600">{{ $entry->download_count }}</span>
                                <p class="text-xs text-gray-400">unduhan</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Most active users --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i class="ti ti-users text-green-500"></i>
                    Pengguna Paling Aktif
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">30 hari terakhir</p>
            </div>

            @if ($mostActiveUsers->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center text-gray-400">
                    <i class="ti ti-users text-3xl mb-2"></i>
                    <p class="text-sm">Belum ada aktivitas</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach ($mostActiveUsers as $index => $entry)
                        <div class="flex items-center gap-3 px-5 py-3">
                            <span class="text-lg font-bold text-gray-200 w-7 shrink-0 text-center">{{ $index + 1 }}</span>
                            <div class="w-8 h-8 rounded-full bg-[#b42b2d] text-white flex items-center justify-center text-xs font-semibold shrink-0">
                                {{ strtoupper(substr($entry->user?->name ?? '?', 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">
                                    {{ $entry->user?->name ?? 'Pengguna dihapus' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $entry->user?->unit?->code }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="text-base font-bold text-green-600">{{ $entry->activity_count }}</span>
                                <p class="text-xs text-gray-400">aktivitas</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
