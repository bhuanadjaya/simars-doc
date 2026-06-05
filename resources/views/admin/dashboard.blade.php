@extends('layouts.app')

@section('title', 'Dashboard — SIMARS-DOC')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium leading-tight">Dokumen Aktif</p>
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center shrink-0">
                    <i class="ti ti-file-check text-green-600 text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalActive) }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium leading-tight">Baru Bulan Ini</p>
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                    <i class="ti ti-file-plus text-blue-600 text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($newThisMonth) }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium leading-tight">Dokumen Obsolet</p>
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center shrink-0">
                    <i class="ti ti-file-x text-orange-500 text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalObsolete) }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium leading-tight">Sudah Direview</p>
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center shrink-0">
                    <i class="ti ti-clipboard-check text-purple-600 text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalReviewed) }}</p>
        </div>

        <a href="{{ route('admin.documents.index', ['expired' => 'soon']) }}"
            class="bg-white border border-orange-200 rounded-xl p-5 shadow-sm hover:border-orange-400 transition-colors block">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-orange-600 uppercase tracking-wide font-medium leading-tight">Akan Expired</p>
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center shrink-0">
                    <i class="ti ti-clock-exclamation text-orange-500 text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-orange-700">{{ number_format($totalExpiringSoon) }}</p>
        </a>

        <a href="{{ route('admin.documents.index', ['expired' => 'overdue']) }}"
            class="bg-white border border-red-200 rounded-xl p-5 shadow-sm hover:border-red-400 transition-colors block">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-red-600 uppercase tracking-wide font-medium leading-tight">Sudah Expired</p>
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center shrink-0">
                    <i class="ti ti-clock-x text-red-500 text-base"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-700">{{ number_format($totalOverdue) }}</p>
        </a>
    </div>

    {{-- Recent activity --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Aktivitas Terbaru</h2>
            <a href="{{ route('admin.reports.activity-log') }}"
                class="text-xs text-blue-600 hover:underline">Lihat semua</a>
        </div>

        @if ($recentActivity->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center text-gray-400">
                <i class="ti ti-activity text-4xl mb-3"></i>
                <p class="text-sm">Belum ada aktivitas tercatat</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach ($recentActivity as $log)
                    <div class="flex items-start gap-3 px-5 py-3">
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
                                    default              => 'ti-activity',
                                };
                            @endphp
                            <i class="ti {{ $icon }} text-sm text-gray-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">{{ $log->user?->name ?? '—' }}</span>
                                <span class="text-gray-500">·</span>
                                <span class="text-gray-600">{{ str_replace('_', ' ', $log->action) }}</span>
                                @if ($log->document)
                                    <span class="text-gray-500">·</span>
                                    <span class="text-gray-500 truncate">{{ $log->document->title }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
