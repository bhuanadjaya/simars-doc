<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMARS-DOC — Sistem Manajemen Dokumen Rumah Sakit</title>

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
        html { scroll-behavior: smooth; }
        .gradient-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #2596be 100%);
        }
        .feature-card:hover { transform: translateY(-4px); }
        .feature-card { transition: transform .2s ease, box-shadow .2s ease; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

{{-- ── HEADER ─────────────────────────────────────────────────────── --}}
<header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="/images/logo.png" class="h-9 w-auto" alt="SIMARS-DOC">
        </div>
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="#fitur" class="hover:text-[#2596be] transition-colors">Fitur</a>
            <a href="#manfaat" class="hover:text-[#2596be] transition-colors">Manfaat</a>
            <a href="#mulai" class="hover:text-[#2596be] transition-colors">Mulai</a>
        </nav>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}"
                class="ina-button ina-button--secondary ina-button--sm">
                Masuk
            </a>
            <a href="{{ route('register') }}"
                class="ina-button ina-button--primary ina-button--sm">
                Daftar Gratis
            </a>
        </div>
    </div>
</header>

{{-- ── HERO ────────────────────────────────────────────────────────── --}}
<section class="gradient-hero pt-16 min-h-screen flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-24 text-center">
        <span class="inline-flex items-center gap-2 bg-white/10 text-white/80 text-xs font-semibold px-4 py-1.5 rounded-full mb-6 border border-white/20">
            <i class="ti ti-shield-check text-sm text-[#60d4f7]"></i>
            Khusus untuk Rumah Sakit Indonesia
        </span>

        <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6">
            Kelola Dokumen Rumah Sakit<br>
            <span class="text-[#60d4f7]">Lebih Mudah & Terstruktur</span>
        </h1>

        <p class="text-lg md:text-xl text-white/70 max-w-2xl mx-auto mb-10">
            SIMARS-DOC adalah sistem manajemen dokumen berbasis web yang dirancang khusus untuk rumah sakit —
            dari SPO, SK, hingga regulasi eksternal, semua terkelola dalam satu platform.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}"
                class="inline-flex items-center gap-2 bg-[#2596be] hover:bg-[#1c7aa8] text-white font-semibold px-8 py-3.5 rounded-xl transition-colors shadow-lg shadow-[#2596be]/30 text-base">
                <i class="ti ti-rocket"></i>
                Mulai Sekarang — Gratis
            </a>
            <a href="#fitur"
                class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-8 py-3.5 rounded-xl transition-colors text-base border border-white/20">
                <i class="ti ti-info-circle"></i>
                Pelajari Lebih Lanjut
            </a>
        </div>

        {{-- Stats --}}
        <div class="mt-20 grid grid-cols-3 gap-8 max-w-lg mx-auto">
            <div class="text-center">
                <p class="text-3xl font-bold text-white">100%</p>
                <p class="text-white/60 text-sm mt-1">Berbasis Web</p>
            </div>
            <div class="text-center border-x border-white/10">
                <p class="text-3xl font-bold text-white">Multi</p>
                <p class="text-white/60 text-sm mt-1">Rumah Sakit</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-white">Aman</p>
                <p class="text-white/60 text-sm mt-1">Akses Terkontrol</p>
            </div>
        </div>
    </div>
</section>

{{-- ── FITUR ───────────────────────────────────────────────────────── --}}
<section id="fitur" class="py-24 bg-gray-50">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto">Semua yang Anda butuhkan untuk mengelola dokumen rumah sakit secara profesional</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Feature 1 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-files text-[#2596be] text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-2">Manajemen Dokumen Lengkap</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Upload, publikasikan, revisi, dan arsipkan dokumen dengan alur kerja yang jelas — dari Draft hingga Aktif dan Obsolete.</p>
            </div>

            {{-- Feature 2 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-building-hospital text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-2">Multi Rumah Sakit</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Satu platform untuk banyak rumah sakit. Data setiap RS terisolasi dengan aman — tidak ada data yang tercampur.</p>
            </div>

            {{-- Feature 3 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-users text-purple-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-2">Manajemen Peran & Akses</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Kontrol akses berbasis peran: Super Admin, Admin Unit, Auditor, dan Pengguna Portal dengan hak akses yang tepat.</p>
            </div>

            {{-- Feature 4 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-gavel text-orange-500 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-2">Regulasi Eksternal</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Simpan dan distribusikan regulasi eksternal (UU, Permenkes, Standar Akreditasi) ke unit yang relevan secara otomatis.</p>
            </div>

            {{-- Feature 5 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-clock-exclamation text-yellow-500 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-2">Pengingat Masa Berlaku</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Tetapkan tanggal kadaluarsa dokumen dan reminder otomatis agar dokumen selalu diperbarui tepat waktu.</p>
            </div>

            {{-- Feature 6 --}}
            <div class="feature-card bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center mb-4">
                    <i class="ti ti-chart-bar text-red-500 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-2">Laporan & Audit</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Ekspor Daftar Induk Dokumen, log aktivitas lengkap, dan statistik penggunaan — siap untuk kebutuhan akreditasi.</p>
            </div>
        </div>
    </div>
</section>

{{-- ── MANFAAT ─────────────────────────────────────────────────────── --}}
<section id="manfaat" class="py-24 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Dirancang untuk<br>
                    <span class="text-[#2596be]">Kebutuhan Akreditasi</span>
                </h2>
                <p class="text-gray-500 text-lg mb-8 leading-relaxed">
                    SIMARS-DOC membantu rumah sakit memenuhi standar dokumentasi yang disyaratkan oleh
                    KARS, JCI, dan regulasi Kemenkes — dengan alur kerja yang terstruktur dan audit trail yang lengkap.
                </p>
                <div class="space-y-4">
                    @foreach([
                        ['ti-check', 'Daftar Induk Dokumen siap ekspor (Excel & PDF)'],
                        ['ti-check', 'Versi dokumen terlacak dengan rantai revisi otomatis'],
                        ['ti-check', 'Notifikasi real-time ke unit terkait saat dokumen baru terbit'],
                        ['ti-check', 'Log aktivitas lengkap untuk keperluan audit'],
                        ['ti-check', 'Pratinjau PDF langsung di browser tanpa download'],
                    ] as [$icon, $text])
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-[#2596be]/10 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                            <i class="ti {{ $icon }} text-[#2596be] text-xs"></i>
                        </div>
                        <p class="text-gray-700 text-sm">{{ $text }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl p-8 border border-gray-100">
                <div class="space-y-4">
                    {{-- Mock document cards --}}
                    @foreach([
                        ['SPO/IGD/001/2025', 'SPO Triase IGD', 'Aktif', 'positive'],
                        ['SK/DIR/012/2025', 'SK Direktur — Keselamatan Pasien', 'Draft', 'warning'],
                        ['PERDIRUT/023/2024', 'Peraturan Direktur Pelayanan', 'Obsolete', 'destructive'],
                    ] as [$num, $title, $status, $badge])
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex items-center gap-3">
                        <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center shrink-0">
                            <i class="ti ti-file-type-pdf text-red-500 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-mono text-gray-400">{{ $num }}</p>
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $title }}</p>
                        </div>
                        <span class="ina-badge ina-badge--{{ $badge }} ina-badge--sm shrink-0">{{ $status }}</span>
                    </div>
                    @endforeach
                    <div class="text-center pt-2">
                        <p class="text-xs text-gray-400">dan ribuan dokumen lainnya…</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── CTA ─────────────────────────────────────────────────────────── --}}
<section id="mulai" class="gradient-hero py-24">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Siap Meningkatkan Tata Kelola Dokumen?
        </h2>
        <p class="text-white/70 text-lg mb-10">
            Daftarkan rumah sakit Anda sekarang — gratis, tanpa kartu kredit, langsung bisa digunakan.
        </p>
        <a href="{{ route('register') }}"
            class="inline-flex items-center gap-2 bg-white text-[#2596be] font-bold px-10 py-4 rounded-xl hover:bg-gray-50 transition-colors shadow-xl text-lg">
            <i class="ti ti-rocket"></i>
            Daftar Sekarang
        </a>
        <p class="text-white/40 text-sm mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-white/70 hover:text-white underline transition-colors">Masuk di sini</a>
        </p>
    </div>
</section>

{{-- ── FOOTER ──────────────────────────────────────────────────────── --}}
<footer class="bg-gray-900 py-10">
    <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <img src="/images/logo.png" class="h-8 w-auto brightness-0 invert opacity-80" alt="SIMARS-DOC">
        </div>
        <p class="text-gray-500 text-sm">© {{ date('Y') }} SIMARS-DOC. Sistem Manajemen Dokumen Rumah Sakit.</p>
        <div class="flex items-center gap-6 text-sm text-gray-500">
            <a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a>
            <a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar</a>
        </div>
    </div>
</footer>

</body>
</html>
