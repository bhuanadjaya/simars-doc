@extends('layouts.app')

@section('title', 'Tambah Unit — SIMARS-DOC')

@section('content')
<div class="max-w-xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.units.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-2">
            <i class="ti ti-arrow-left text-sm"></i>
            Kembali ke daftar
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Unit Baru</h1>
    </div>

    <form method="POST" action="{{ route('admin.units.store') }}">
        @csrf

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-4 space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="ina-text-field">
                    <label class="ina-text-field__label">Kode Unit <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <input type="text" name="code" class="ina-text-field__input @error('code') border-red-500 @enderror"
                            value="{{ old('code') }}" placeholder="misal: IGD" style="text-transform: uppercase;">
                    </div>
                    @error('code') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="ina-text-field">
                    <label class="ina-text-field__label">Nama Unit <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <input type="text" name="name" class="ina-text-field__input @error('name') border-red-500 @enderror"
                            value="{{ old('name') }}" placeholder="Nama lengkap unit">
                    </div>
                    @error('name') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="ina-text-field">
                <label class="ina-text-field__label">Unit Induk (Opsional)</label>
                <div class="ina-text-field__wrapper">
                    <select name="parent_id" class="ina-text-field__input @error('parent_id') border-red-500 @enderror">
                        <option value="">— Tidak ada (unit root) —</option>
                        @foreach ($parentUnits as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') === $parent->id ? 'selected' : '' }}>
                                {{ $parent->code }} — {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('parent_id') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.units.index') }}" class="ina-button ina-button--secondary ina-button--md">Batal</a>
            <button type="submit" class="ina-button ina-button--primary ina-button--md flex items-center gap-2">
                <i class="ti ti-building-plus text-sm"></i>
                Tambah Unit
            </button>
        </div>
    </form>

</div>
@endsection
