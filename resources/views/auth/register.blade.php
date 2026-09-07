<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Rumah Sakit — SIMARS-DOC</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" href="https://unpkg.com/@idds/styles@latest/dist/index.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        :root, [data-brand] {
            --ina-primary-primary: #2596be;
            --ina-primary-600: #1c7aa8;
            --ina-primary-700: #166d96;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-6 h-14 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <img src="/images/logo.png" class="h-8 w-auto" alt="SIMARS-DOC">
            </a>
            <p class="text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#2596be] font-medium hover:underline">Masuk</a>
            </p>
        </div>
    </header>

    <div class="max-w-2xl mx-auto px-6 py-12">

        {{-- Page header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Daftarkan Rumah Sakit Anda</h1>
            <p class="text-gray-500 mt-2 text-sm">Isi data di bawah untuk membuat akun dan mulai menggunakan SIMARS-DOC.</p>
        </div>

        {{-- Alerts --}}
        @if (session('error'))
            <div class="flex items-start gap-3 p-4 mb-6 bg-red-50 border border-red-200 rounded-xl">
                <i class="ti ti-alert-circle text-red-500 text-lg shrink-0 mt-0.5"></i>
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-start gap-3 p-4 mb-6 bg-red-50 border border-red-200 rounded-xl">
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

        <form method="POST" action="{{ route('register') }}" id="register-form">
            @csrf

            {{-- Section 1: Hospital --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mb-5">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 bg-[#2596be]/10 rounded-lg flex items-center justify-center">
                        <i class="ti ti-building-hospital text-[#2596be] text-base"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Informasi Rumah Sakit</h2>
                        <p class="text-xs text-gray-400">Data identitas rumah sakit Anda</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="ina-text-field">
                        <label class="ina-text-field__label" for="hospital_name">
                            Nama Rumah Sakit <span class="text-red-500">*</span>
                        </label>
                        <div class="ina-text-field__wrapper {{ $errors->has('hospital_name') ? 'ina-text-field__wrapper--status-error' : '' }}">
                            <input type="text" id="hospital_name" name="hospital_name"
                                class="ina-text-field__input"
                                placeholder="e.g. RSUD Bhuana Djaya"
                                value="{{ old('hospital_name') }}" required maxlength="255">
                        </div>
                        @error('hospital_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="ina-text-field">
                        <label class="ina-text-field__label" for="hospital_code">
                            Kode Rumah Sakit <span class="text-red-500">*</span>
                        </label>
                        <div class="ina-text-field__wrapper {{ $errors->has('hospital_code') ? 'ina-text-field__wrapper--status-error' : '' }}">
                            <input type="text" id="hospital_code" name="hospital_code"
                                class="ina-text-field__input font-mono uppercase"
                                placeholder="e.g. RSBD"
                                value="{{ old('hospital_code') }}" required maxlength="50">
                        </div>
                        <p class="text-gray-400 text-xs mt-1">Singkatan unik, akan digunakan sebagai identifikasi. Otomatis diubah ke huruf kapital.</p>
                        @error('hospital_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="ina-text-field">
                            <label class="ina-text-field__label" for="hospital_email">Email RS</label>
                            <div class="ina-text-field__wrapper {{ $errors->has('hospital_email') ? 'ina-text-field__wrapper--status-error' : '' }}">
                                <input type="email" id="hospital_email" name="hospital_email"
                                    class="ina-text-field__input"
                                    placeholder="info@rumahsakit.id"
                                    value="{{ old('hospital_email') }}">
                            </div>
                            @error('hospital_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="ina-text-field">
                            <label class="ina-text-field__label" for="hospital_phone">Telepon RS</label>
                            <div class="ina-text-field__wrapper {{ $errors->has('hospital_phone') ? 'ina-text-field__wrapper--status-error' : '' }}">
                                <input type="text" id="hospital_phone" name="hospital_phone"
                                    class="ina-text-field__input"
                                    placeholder="021-xxxxxxx"
                                    value="{{ old('hospital_phone') }}">
                            </div>
                            @error('hospital_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="ina-text-field">
                        <label class="ina-text-field__label" for="hospital_address">Alamat RS</label>
                        <div class="ina-text-field__wrapper {{ $errors->has('hospital_address') ? 'ina-text-field__wrapper--status-error' : '' }}">
                            <textarea id="hospital_address" name="hospital_address"
                                class="ina-text-field__input min-h-[70px] resize-y"
                                placeholder="Jl. Contoh No. 1, Jakarta...">{{ old('hospital_address') }}</textarea>
                        </div>
                        @error('hospital_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Admin User --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mb-6">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                        <i class="ti ti-user-shield text-purple-600 text-base"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Akun Administrator</h2>
                        <p class="text-xs text-gray-400">Akan menjadi Super Admin untuk rumah sakit ini</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="ina-text-field">
                        <label class="ina-text-field__label" for="name">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <div class="ina-text-field__wrapper {{ $errors->has('name') ? 'ina-text-field__wrapper--status-error' : '' }}">
                            <input type="text" id="name" name="name"
                                class="ina-text-field__input"
                                placeholder="Nama lengkap Anda"
                                value="{{ old('name') }}" required maxlength="255">
                        </div>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="ina-text-field">
                        <label class="ina-text-field__label" for="email">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <div class="ina-text-field__wrapper {{ $errors->has('email') ? 'ina-text-field__wrapper--status-error' : '' }}">
                            <input type="email" id="email" name="email"
                                class="ina-text-field__input"
                                placeholder="admin@rumahsakit.id"
                                value="{{ old('email') }}" required>
                        </div>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="ina-text-field">
                            <label class="ina-text-field__label" for="password">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="ina-text-field__wrapper {{ $errors->has('password') ? 'ina-text-field__wrapper--status-error' : '' }}">
                                <input type="password" id="password" name="password"
                                    class="ina-text-field__input"
                                    placeholder="Min. 8 karakter" required>
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="ina-text-field">
                            <label class="ina-text-field__label" for="password_confirmation">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="ina-text-field__wrapper">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="ina-text-field__input"
                                    placeholder="Ulangi password" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terms note --}}
            <p class="text-xs text-gray-400 text-center mb-5">
                Dengan mendaftar, Anda setuju bahwa data yang dimasukkan adalah benar dan akurat.
                Unit kerja default akan dibuat otomatis dan dapat diubah setelah masuk.
            </p>

            <button type="submit" id="submit-btn"
                class="ina-button ina-button--primary ina-button--md w-full flex items-center justify-center gap-2 text-base py-3">
                <i class="ti ti-rocket"></i>
                <span>Daftarkan Rumah Sakit</span>
            </button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    $(document).ready(function () {
        // Auto uppercase hospital code
        $('#hospital_code').on('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });

        // Disable submit on submit
        $('#register-form').on('submit', function () {
            $('#submit-btn').prop('disabled', true)
                .html('<i class="ti ti-loader-2 animate-spin"></i> <span>Memproses...</span>');
        });
    });
    </script>
</body>
</html>
