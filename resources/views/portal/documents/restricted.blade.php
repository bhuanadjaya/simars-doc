@extends('layouts.app')

@section('title', 'Akses Terbatas — SIMARS-DOC')

@section('content')
<div class="max-w-md mx-auto mt-16 text-center">
    <div class="w-16 h-16 bg-amber-50 border border-amber-200 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="ti ti-lock text-amber-500 text-3xl"></i>
    </div>
    <h1 class="text-xl font-bold text-gray-900 mb-2">Dokumen Terbatas</h1>
    <p class="text-sm text-gray-500 mb-6">
        Dokumen ini bersifat terbatas dan hanya dapat diakses oleh pengunggah dokumen tersebut.
    </p>
    <a href="{{ route('portal.documents.index') }}" class="ina-button ina-button--secondary ina-button--md">
        <i class="ti ti-arrow-left text-sm mr-1"></i> Kembali ke Portal
    </a>
</div>
@endsection
