@extends('layouts.app')

@section('title', 'Tambah Jenis Dokumen — SIMARS-DOC')

@section('content')
<div class="max-w-xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Jenis Dokumen</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tambahkan jenis/tipe dokumen baru ke sistem.</p>
        </div>
        <a href="{{ route('admin.document-types.index') }}"
            class="ina-button ina-button--secondary ina-button--sm flex items-center gap-2">
            <i class="ti ti-arrow-left text-sm"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-xl">
            <i class="ti ti-alert-circle text-red-500 text-lg shrink-0 mt-0.5"></i>
            <div>
                <p class="text-sm font-medium text-red-700 mb-1">Periksa isian berikut:</p>
                <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.document-types.store') }}">
            @csrf

            <div class="space-y-4">
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="code">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('code') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="text" id="code" name="code"
                            class="ina-text-field__input font-mono"
                            placeholder="e.g. SPO, SK, IK"
                            value="{{ old('code') }}"
                            maxlength="20" required>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Singkatan unik, maksimal 20 karakter.</p>
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="name">
                        Nama Jenis Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('name') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input type="text" id="name" name="name"
                            class="ina-text-field__input"
                            placeholder="e.g. Standar Prosedur Operasional"
                            value="{{ old('name') }}"
                            maxlength="100" required>
                    </div>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                <a href="{{ route('admin.document-types.index') }}"
                    class="ina-button ina-button--secondary ina-button--md">Batal</a>
                <button type="submit" class="ina-button ina-button--primary ina-button--md flex items-center gap-2">
                    <i class="ti ti-plus text-sm"></i> Simpan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
