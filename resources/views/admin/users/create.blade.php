@extends('layouts.app')

@section('title', 'Tambah Pengguna — SIMARS-DOC')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 mb-2">
            <i class="ti ti-arrow-left text-sm"></i>
            Kembali ke daftar
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Pengguna Baru</h1>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-4 space-y-4">

            <div class="ina-text-field">
                <label class="ina-text-field__label">Nama Lengkap <span class="text-red-500">*</span></label>
                <div class="ina-text-field__wrapper">
                    <input type="text" name="name" class="ina-text-field__input @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" placeholder="Nama lengkap pengguna">
                </div>
                @error('name') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="ina-text-field">
                    <label class="ina-text-field__label">NIP / ID Pegawai</label>
                    <div class="ina-text-field__wrapper">
                        <input type="text" name="employee_id" class="ina-text-field__input @error('employee_id') border-red-500 @enderror"
                            value="{{ old('employee_id') }}" placeholder="Opsional">
                    </div>
                    @error('employee_id') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="ina-text-field">
                    <label class="ina-text-field__label">Email <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <input type="email" name="email" class="ina-text-field__input @error('email') border-red-500 @enderror"
                            value="{{ old('email') }}" placeholder="email@rumahsakit.id">
                    </div>
                    @error('email') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="ina-text-field">
                    <label class="ina-text-field__label">Unit <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <select name="unit_id" class="ina-text-field__input @error('unit_id') border-red-500 @enderror">
                            <option value="">— Pilih Unit —</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') === $unit->id ? 'selected' : '' }}>
                                    {{ $unit->code }} — {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('unit_id') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="ina-text-field">
                    <label class="ina-text-field__label">Role <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <select name="role_id" class="ina-text-field__input @error('role_id') border-red-500 @enderror">
                            <option value="">— Pilih Role —</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') === $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('role_id') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="ina-text-field">
                    <label class="ina-text-field__label">Password <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <input type="password" name="password" class="ina-text-field__input @error('password') border-red-500 @enderror"
                            placeholder="Minimal 8 karakter">
                    </div>
                    @error('password') <p class="ina-text-field__helper-text text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="ina-text-field">
                    <label class="ina-text-field__label">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <div class="ina-text-field__wrapper">
                        <input type="password" name="password_confirmation" class="ina-text-field__input"
                            placeholder="Ulangi password">
                    </div>
                </div>
            </div>

        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="ina-button ina-button--secondary ina-button--md">Batal</a>
            <button type="submit" class="ina-button ina-button--primary ina-button--md flex items-center gap-2">
                <i class="ti ti-user-plus text-sm"></i>
                Tambah Pengguna
            </button>
        </div>
    </form>

</div>
@endsection
