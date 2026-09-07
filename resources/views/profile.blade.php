@extends('layouts.app')

@section('title', 'Profil Saya — SIMARS-DOC')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun Anda.</p>
    </div>

    {{-- Section 1: Informasi Profil --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="ti ti-user text-[#2596be]"></i> Informasi Profil
        </h2>

        @if (session('success'))
            <div class="flex items-center gap-2 p-3 mb-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                <i class="ti ti-circle-check shrink-0"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="name">Nama Lengkap</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('name') ? 'ina-text-field__wrapper--status-error' : '' }}">
                        <input type="text" id="name" name="name" class="ina-text-field__input"
                            value="{{ old('name', auth()->user()->name) }}" required maxlength="255">
                    </div>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="email">Email</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('email') ? 'ina-text-field__wrapper--status-error' : '' }}">
                        <input type="email" id="email" name="email" class="ina-text-field__input"
                            value="{{ old('email', auth()->user()->email) }}" required>
                    </div>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="ina-text-field">
                    <label class="ina-text-field__label">Role</label>
                    <div class="ina-text-field__wrapper">
                        <input type="text" class="ina-text-field__input bg-gray-50 text-gray-500"
                            value="{{ auth()->user()->role?->name ?? '—' }}" disabled>
                    </div>
                </div>

                @if (auth()->user()->hospital)
                    <div class="ina-text-field">
                        <label class="ina-text-field__label">Rumah Sakit</label>
                        <div class="ina-text-field__wrapper">
                            <input type="text" class="ina-text-field__input bg-gray-50 text-gray-500"
                                value="{{ auth()->user()->hospital->name }}" disabled>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-5">
                <button type="submit" class="ina-button ina-button--primary ina-button--md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Section 2: Ganti Password --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="ti ti-lock text-[#2596be]"></i> Ganti Password
        </h2>

        @if (session('success_password'))
            <div class="flex items-center gap-2 p-3 mb-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                <i class="ti ti-circle-check shrink-0"></i> {{ session('success_password') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="current_password">Password Saat Ini</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('current_password') ? 'ina-text-field__wrapper--status-error' : '' }}">
                        <input type="password" id="current_password" name="current_password"
                            class="ina-text-field__input" placeholder="••••••••" required>
                    </div>
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="ina-text-field">
                        <label class="ina-text-field__label" for="password">Password Baru</label>
                        <div class="ina-text-field__wrapper {{ $errors->has('password') ? 'ina-text-field__wrapper--status-error' : '' }}">
                            <input type="password" id="password" name="password"
                                class="ina-text-field__input" placeholder="Min. 8 karakter" required>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="ina-text-field">
                        <label class="ina-text-field__label" for="password_confirmation">Konfirmasi Password</label>
                        <div class="ina-text-field__wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="ina-text-field__input" placeholder="Ulangi password baru" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <button type="submit" class="ina-button ina-button--primary ina-button--md">
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
