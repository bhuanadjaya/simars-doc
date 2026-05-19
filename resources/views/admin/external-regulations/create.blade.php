@extends('layouts.app')

@section('title', 'Tambah Regulasi Eksternal — SIMARS-DOC')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.external-regulations.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-2">
            <i class="ti ti-arrow-left text-sm"></i>
            Kembali ke daftar
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Regulasi Eksternal</h1>
    </div>

    <form method="POST" action="{{ route('admin.external-regulations.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-4">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Identitas Regulasi</h2>

            <div class="space-y-4">
                <div class="ina-text-field">
                    <label class="ina-text-field__label">Nomor Regulasi <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <input type="text" name="regulation_number" class="ina-text-field__input @error('regulation_number') border-red-500 @enderror"
                            value="{{ old('regulation_number') }}" placeholder="misal: UU No. 44 Tahun 2009">
                    </div>
                    @error('regulation_number') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="ina-text-field">
                    <label class="ina-text-field__label">Judul <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <input type="text" name="title" class="ina-text-field__input @error('title') border-red-500 @enderror"
                            value="{{ old('title') }}" placeholder="Judul regulasi">
                    </div>
                    @error('title') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="ina-text-field">
                        <label class="ina-text-field__label">Instansi Penerbit <span class="text-red-500">*</span></label>
                        <div class="ina-text-field__wrapper">
                            <input type="text" name="issuing_agency" class="ina-text-field__input @error('issuing_agency') border-red-500 @enderror"
                                value="{{ old('issuing_agency') }}" placeholder="misal: Kemenkes RI">
                        </div>
                        @error('issuing_agency') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="ina-text-field">
                        <label class="ina-text-field__label">Kategori <span class="text-red-500">*</span></label>
                        <div class="ina-text-field__wrapper">
                            <select name="category" class="ina-text-field__input @error('category') border-red-500 @enderror">
                                <option value="">— Pilih Kategori —</option>
                                @foreach ($categories as $val => $label)
                                    <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('category') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="ina-text-field">
                        <label class="ina-text-field__label">Tanggal Terbit <span class="text-red-500">*</span></label>
                        <div class="ina-text-field__wrapper">
                            <input type="date" name="issued_date" class="ina-text-field__input @error('issued_date') border-red-500 @enderror"
                                value="{{ old('issued_date') }}">
                        </div>
                        @error('issued_date') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="ina-text-field">
                        <label class="ina-text-field__label">Tanggal Berlaku <span class="text-red-500">*</span></label>
                        <div class="ina-text-field__wrapper">
                            <input type="date" name="effective_date" class="ina-text-field__input @error('effective_date') border-red-500 @enderror"
                                value="{{ old('effective_date') }}">
                        </div>
                        @error('effective_date') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-4">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Unit Terdampak</h2>
            <p class="text-sm text-gray-500 mb-4">Opsional. Pengguna di unit terpilih akan mendapat notifikasi.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach ($units as $unit)
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" name="affected_unit_ids[]" value="{{ $unit->id }}"
                            {{ in_array($unit->id, old('affected_unit_ids', [])) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-blue-600">
                        <span>{{ $unit->code }} — {{ $unit->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('affected_unit_ids') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">File Regulasi</h2>
            <p class="text-sm text-gray-500 mb-4">PDF wajib, maks 20MB.</p>

            <div class="ina-text-field">
                <label class="ina-text-field__label">File PDF <span class="text-red-500">*</span></label>
                <div class="ina-text-field__wrapper">
                    <input type="file" name="pdf_file" accept=".pdf"
                        class="ina-text-field__input @error('pdf_file') border-red-500 @enderror">
                </div>
                @error('pdf_file') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.external-regulations.index') }}"
                class="ina-button ina-button--secondary ina-button--md">Batal</a>
            <button type="submit" class="ina-button ina-button--primary ina-button--md flex items-center gap-2">
                <i class="ti ti-upload text-sm"></i>
                Unggah Regulasi
            </button>
        </div>
    </form>

</div>
@endsection
