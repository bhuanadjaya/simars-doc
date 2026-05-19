@extends('layouts.app')

@section('title', 'Dashboard — SIMARS-DOC')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Dokumen Aktif</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">—</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Dokumen Baru Bulan Ini</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">—</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Dokumen Kadaluarsa</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">—</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <p class="text-sm text-gray-400 text-center py-8">Dashboard lengkap akan diimplementasikan pada F12.</p>
    </div>
@endsection
