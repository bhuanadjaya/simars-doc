<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — SIMARS-DOC</title>

    <link rel="stylesheet" href="https://unpkg.com/@idds/styles@latest/dist/index.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        :root,
        [data-brand] {
            --ina-primary-primary: #2596be;
            --ina-primary-600: #1c7aa8;
            --ina-primary-700: #166d96;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-md overflow-hidden">

        {{-- Header banner --}}
        {{-- <div class="bg-[#b42b2d] px-8 py-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center text-white font-bold text-sm shrink-0">
                    RS
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg leading-tight">SIMARS-DOC</h1>
                    <p class="text-white/75 text-xs">Sistem Manajemen Dokumen Rumah Sakit</p>
                </div>
            </div>
        </div> --}}

        {{-- Form area --}}
        <div class="px-8 py-8 space-y-5">
            <img src="/images/logo.png" class="max-w-sm w-full mb-4 self-center" />
            <div>
                <h2 class="text-xl font-bold text-gray-900">Masuk</h2>
                <p class="text-sm text-gray-500 mt-1">Masukkan kredensial akun Anda untuk melanjutkan</p>
            </div>

            {{-- Error alert --}}
            @if ($errors->any())
                <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <i class="ti ti-alert-circle text-red-500 mt-0.5 text-lg shrink-0"></i>
                    <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="email">Email</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('email') ? 'ina-text-field__wrapper--error' : '' }}">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="ina-text-field__input"
                            placeholder="nama@rumahsakit.id"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="ina-password-input">
                    <label class="ina-password-input__label" for="password">Password</label>
                    <div class="ina-password-input__wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="ina-password-input__input"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="ina-password-input__toggle" id="toggle-password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                id="icon-eye-off">
                                <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"></path>
                                <path d="M16.681 16.673a8.717 8.717 0 0 1-4.681 1.327c-3.6 0-6.6-2-9-6 1.272-2.12 2.712-3.678 4.32-4.674m2.86-1.146a9.055 9.055 0 0 1 1.82-.18c3.6 0 6.6 2 9 6-.666 1.11-1.379 2.067-2.138 2.87"></path>
                                <path d="M3 3l18 18"></path>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="hidden" id="icon-eye">
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0-4 0"></path>
                                <path d="M21 12c-2.4 4-5.4 6-9 6c-3.6 0-6.6-2-9-6c2.4-4 5.4-6 9-6c3.6 0 6.6 2 9 6"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="ina-button ina-button--primary ina-button--md w-full mt-2">
                    <span class="ina-button__text">Masuk</span>
                </button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/@idds/js@latest/dist/index.iife.js"></script>
    <script>
    $(document).ready(function () {
        $('#toggle-password').on('click', function () {
            const $input = $('#password');
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $('#icon-eye-off').addClass('hidden');
                $('#icon-eye').removeClass('hidden');
            } else {
                $input.attr('type', 'password');
                $('#icon-eye-off').removeClass('hidden');
                $('#icon-eye').addClass('hidden');
            }
        });
    });
    </script>
</body>
</html>
