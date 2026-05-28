@extends('layouts.app')

@section('title', 'Jenis Dokumen — SIMARS-DOC')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Jenis Dokumen</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola jenis/tipe dokumen yang tersedia di sistem.</p>
        </div>
        <a href="{{ route('admin.document-types.create') }}"
            class="ina-button ina-button--primary ina-button--md flex items-center gap-2">
            <i class="ti ti-plus text-sm"></i>
            Tambah Jenis
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-start gap-3 p-4 mb-5 bg-green-50 border border-green-200 rounded-xl">
            <i class="ti ti-circle-check text-green-500 text-lg shrink-0 mt-0.5"></i>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-xl">
            <i class="ti ti-alert-circle text-red-500 text-lg shrink-0 mt-0.5"></i>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.document-types.index') }}" id="filter-form">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px] ina-text-field">
                    <div class="ina-text-field__wrapper">
                        <span class="ina-text-field__icon-left"><i class="ti ti-search text-gray-400 text-lg"></i></span>
                        <input type="text" name="q" class="ina-text-field__input pl-9 text-sm"
                            placeholder="Cari kode atau nama jenis dokumen..."
                            value="{{ request('q') }}" autocomplete="off">
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
                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.document-types.index') }}"
                        class="ina-button ina-button--secondary ina-button--md text-red-500">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if ($types->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="ti ti-file-description text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500">Belum ada jenis dokumen terdaftar</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Jenis</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Dokumen</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($types as $type)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-700">{{ $type->code }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $type->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $type->documents_count }}</td>
                            <td class="px-4 py-3">
                                @if ($type->is_active)
                                    <span class="ina-badge ina-badge--positive ina-badge--sm">Aktif</span>
                                @else
                                    <span class="ina-badge ina-badge--neutral ina-badge--sm">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <a href="{{ route('admin.document-types.edit', $type) }}"
                                        class="ina-button ina-button--secondary ina-button--sm"
                                        title="Edit">
                                        <i class="ti ti-pencil text-sm"></i>
                                    </a>

                                    @if ($type->is_active)
                                        <form method="POST" action="{{ route('admin.document-types.deactivate', $type) }}"
                                            class="form-deactivate inline">
                                            @csrf
                                            <button type="submit"
                                                class="ina-button ina-button--sm bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100"
                                                title="Nonaktifkan">
                                                <i class="ti ti-eye-off text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.document-types.activate', $type) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="ina-button ina-button--secondary ina-button--sm text-green-600"
                                                title="Aktifkan kembali">
                                                <i class="ti ti-eye text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.document-types.destroy', $type) }}"
                                        class="form-delete inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="ina-button ina-button--sm bg-red-50 text-red-700 border border-red-200 hover:bg-red-100"
                                            title="Hapus"
                                            data-name="{{ $type->name }}"
                                            data-count="{{ $type->documents_count }}">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($types->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        {{ $types->firstItem() }}–{{ $types->lastItem() }} dari {{ $types->total() }} jenis dokumen
                    </p>
                    <div class="flex gap-1">
                        @if ($types->onFirstPage())
                            <span class="ina-button ina-button--secondary ina-button--sm opacity-40 !px-2 cursor-not-allowed">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </span>
                        @else
                            <a href="{{ $types->previousPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
                                <i class="ti ti-chevron-left text-sm"></i>
                            </a>
                        @endif
                        @if ($types->hasMorePages())
                            <a href="{{ $types->nextPageUrl() }}" class="ina-button ina-button--secondary ina-button--sm !px-2">
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

    $('.form-deactivate').on('submit', function (e) {
        e.preventDefault();
        if (confirm('Nonaktifkan jenis dokumen ini? Jenis dokumen yang memiliki dokumen aktif tidak dapat dinonaktifkan.')) {
            this.submit();
        }
    });

    $('.form-delete').on('submit', function (e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        const name = btn.data('name');
        const count = parseInt(btn.data('count'));
        if (count > 0) {
            alert('Jenis dokumen "' + name + '" tidak dapat dihapus karena masih memiliki ' + count + ' dokumen terkait.');
            return;
        }
        if (confirm('Hapus jenis dokumen "' + name + '"? Tindakan ini tidak dapat dibatalkan.')) {
            this.submit();
        }
    });
});
</script>
@endpush
