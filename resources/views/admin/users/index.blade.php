@extends('layouts.app')

@section('title', 'Manajemen Pengguna — SIMARS-DOC')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola akun pengguna sistem.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="ina-button ina-button--primary ina-button--md flex items-center gap-2">
            <i class="ti ti-plus text-sm"></i>
            Tambah Pengguna
        </a>
    </div>

    {{-- Password flash --}}
    @if (session('generated_password'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 flex items-start gap-3">
            <i class="ti ti-key text-green-600 text-xl mt-0.5 shrink-0"></i>
            <div>
                <p class="text-sm font-medium text-green-800">Password baru untuk <strong>{{ session('generated_password_user') }}</strong></p>
                <p class="text-sm text-green-700 mt-1 font-mono bg-green-100 rounded px-2 py-1 inline-block">{{ session('generated_password') }}</p>
                <p class="text-xs text-green-600 mt-1">Salin dan berikan kepada pengguna. Password ini hanya ditampilkan sekali.</p>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px] ina-text-field">
                    <div class="ina-text-field__wrapper">
                        <span class="ina-text-field__icon-left"><i class="ti ti-search text-gray-400 text-lg"></i></span>
                        <input type="text" name="q" class="ina-text-field__input pl-9 text-sm"
                            placeholder="Cari nama, email, atau NIP..."
                            value="{{ request('q') }}" autocomplete="off">
                    </div>
                </div>
                <div class="ina-text-field w-44">
                    <label class="ina-text-field__label text-xs">Unit</label>
                    <div class="ina-text-field__wrapper">
                        <select name="unit" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit') === $unit->id ? 'selected' : '' }}>
                                    {{ $unit->code }} — {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ina-text-field w-40">
                    <label class="ina-text-field__label text-xs">Role</label>
                    <div class="ina-text-field__wrapper">
                        <select name="role" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role') === $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ina-text-field w-36">
                    <label class="ina-text-field__label text-xs">Status</label>
                    <div class="ina-text-field__wrapper">
                        <select name="status" class="ina-text-field__input text-sm filter-select">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="ina-button ina-button--primary ina-button--md px-5">Cari</button>
                @if (request()->hasAny(['q', 'unit', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}"
                        class="ina-button ina-button--secondary ina-button--md text-red-500">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if ($users->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="ti ti-users text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500">Tidak ada pengguna ditemukan</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Login Terakhir</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#2596be] text-white flex items-center justify-center text-xs font-semibold shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                        @if ($user->employee_id)
                                            <div class="text-xs text-gray-400 font-mono">{{ $user->employee_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $user->unit?->code }}</td>
                            <td class="px-4 py-3">
                                <span class="ina-badge ina-badge--info ina-badge--sm">{{ $user->role?->name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($user->is_active)
                                    <span class="ina-badge ina-badge--positive ina-badge--sm">Aktif</span>
                                @else
                                    <span class="ina-badge ina-badge--neutral ina-badge--sm">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="ina-button ina-button--secondary ina-button--sm">
                                        <i class="ti ti-pencil text-sm"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}"
                                        class="form-reset inline">
                                        @csrf
                                        <button type="submit"
                                            class="ina-button ina-button--secondary ina-button--sm"
                                            title="Reset Password">
                                            <i class="ti ti-key text-sm"></i>
                                        </button>
                                    </form>
                                    @if ($user->is_active)
                                        <form method="POST" action="{{ route('admin.users.deactivate', $user) }}"
                                            class="form-deactivate inline">
                                            @csrf
                                            <button type="submit"
                                                class="ina-button ina-button--danger ina-button--sm"
                                                title="Nonaktifkan">
                                                <i class="ti ti-user-off text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.activate', $user) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="ina-button ina-button--secondary ina-button--sm text-green-600"
                                                title="Aktifkan kembali">
                                                <i class="ti ti-user-check text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($users->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
                    </p>
                    <div class="flex gap-1">
                        @if ($users->onFirstPage())
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 !px-2 cursor-not-allowed">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </a>
                        @endif
                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-right text-sm"></i>
                            </a>
                        @else
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 !px-2 cursor-not-allowed">
                                <i class="ti ti-chevron-right text-sm"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('.filter-select').on('change', function () { $('#filter-form').submit(); });

    $('.form-reset').on('submit', function (e) {
        e.preventDefault();
        if (confirm('Reset password pengguna ini? Password baru akan ditampilkan sekali saja.')) {
            this.submit();
        }
    });

    $('.form-deactivate').on('submit', function (e) {
        e.preventDefault();
        if (confirm('Nonaktifkan akun pengguna ini? Pengguna tidak akan dapat login.')) {
            this.submit();
        }
    });
});
</script>
@endpush
