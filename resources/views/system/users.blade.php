@extends('layouts.app')

@section('title', 'Daftar Pengguna — System Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Semua Pengguna</h1>
    <p class="text-sm text-gray-500 mt-1">Seluruh pengguna lintas rumah sakit.</p>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('system.users') }}" id="filter-form">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48 ina-text-field">
            <div class="ina-text-field__wrapper">
                <span class="ina-text-field__icon-left"><i class="ti ti-search text-gray-400 text-lg"></i></span>
                <input type="text" name="q" class="ina-text-field__input pl-9"
                    placeholder="Cari nama atau email..." value="{{ request('q') }}" autocomplete="off">
            </div>
        </div>
        <div class="ina-text-field w-52">
            <div class="ina-text-field__wrapper">
                <select name="hospital" class="ina-text-field__input text-sm filter-select">
                    <option value="">Semua RS</option>
                    @foreach ($hospitals as $h)
                        <option value="{{ $h->id }}" {{ request('hospital') == $h->id ? 'selected' : '' }}>
                            {{ $h->code }} — {{ $h->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="ina-text-field w-40">
            <div class="ina-text-field__wrapper">
                <select name="role" class="ina-text-field__input text-sm filter-select">
                    <option value="">Semua Role</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin_unit" {{ request('role') === 'admin_unit' ? 'selected' : '' }}>Admin Unit</option>
                    <option value="auditor" {{ request('role') === 'auditor' ? 'selected' : '' }}>Auditor</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
        </div>
        <button type="submit" class="ina-button ina-button--primary ina-button--md">Cari</button>
        @if (request()->hasAny(['q', 'hospital', 'role']))
            <a href="{{ route('system.users') }}" class="ina-button ina-button--secondary ina-button--md text-red-500">
                <i class="ti ti-x text-sm"></i> Reset
            </a>
        @endif
    </div>
</form>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100">
        <p class="text-sm text-gray-500">{{ $users->total() }} pengguna</p>
    </div>

    @if ($users->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center text-gray-400">
            <i class="ti ti-users text-5xl mb-3"></i>
            <p class="text-sm">Tidak ada pengguna ditemukan</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pengguna</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rumah Sakit</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Unit</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Bergabung</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($user->hospital)
                                <p class="text-gray-700">{{ $user->hospital->name }}</p>
                                <p class="text-xs font-mono text-gray-400">{{ $user->hospital->code }}</p>
                            @else
                                <span class="text-xs text-gray-400 italic">System</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-600">
                            {{ $user->unit?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="ina-badge ina-badge--neutral ina-badge--sm">{{ $user->role?->name ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if ($user->is_active)
                                <span class="ina-badge ina-badge--positive ina-badge--sm">Aktif</span>
                            @else
                                <span class="ina-badge ina-badge--destructive ina-badge--sm">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center text-xs text-gray-400">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100">
                <p class="text-xs text-gray-500">Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</p>
                <div class="flex items-center gap-1">
                    @if ($users->onFirstPage())
                        <span class="ina-button ina-button--secondary ina-button--sm opacity-40 cursor-not-allowed !px-2"><i class="ti ti-chevron-left text-sm"></i></span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2"><i class="ti ti-chevron-left text-sm"></i></a>
                    @endif
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2"><i class="ti ti-chevron-right text-sm"></i></a>
                    @else
                        <span class="ina-button ina-button--secondary ina-button--sm opacity-40 cursor-not-allowed !px-2"><i class="ti ti-chevron-right text-sm"></i></span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('.filter-select').on('change', function () { $('#filter-form').submit(); });
});
</script>
@endpush
