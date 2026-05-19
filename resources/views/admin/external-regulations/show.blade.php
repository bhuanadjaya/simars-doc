@extends('layouts.app')

@section('title', $externalRegulation->title . ' — SIMARS-DOC')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="mb-4">
        <a href="{{ route('admin.external-regulations.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600">
            <i class="ti ti-arrow-left text-sm"></i>
            Kembali ke daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: metadata --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 bg-red-50 border border-red-100 rounded-lg flex items-center justify-center shrink-0">
                        <i class="ti ti-file-type-pdf text-red-500 text-xl"></i>
                    </div>
                    <span class="ina-badge ina-badge--info ina-badge--sm">{{ $externalRegulation->category_label }}</span>
                </div>

                <h1 class="text-base font-bold text-gray-900 leading-snug mb-1">{{ $externalRegulation->title }}</h1>
                <p class="text-xs font-mono text-gray-400 mb-4">{{ $externalRegulation->regulation_number }}</p>

                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Instansi Penerbit</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $externalRegulation->issuing_agency }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Tanggal Terbit</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $externalRegulation->issued_date->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Tanggal Berlaku</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $externalRegulation->effective_date->format('d/m/Y') }}</dd>
                    </div>
                    @if ($affectedUnits->isNotEmpty())
                        <div>
                            <dt class="text-xs text-gray-400 uppercase tracking-wide">Unit Terdampak</dt>
                            <dd class="mt-0.5 space-y-1">
                                @foreach ($affectedUnits as $unit)
                                    <div class="text-sm text-gray-700">{{ $unit->code }} — {{ $unit->name }}</div>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Diunggah oleh</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $externalRegulation->uploader?->name ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Actions --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 space-y-2">
                <a href="{{ route('admin.external-regulations.download', $externalRegulation) }}"
                    class="ina-button ina-button--primary ina-button--md w-full flex items-center justify-center gap-2">
                    <i class="ti ti-download"></i>
                    Unduh PDF
                </a>
                <a href="{{ route('admin.external-regulations.edit', $externalRegulation) }}"
                    class="ina-button ina-button--secondary ina-button--md w-full flex items-center justify-center gap-2">
                    <i class="ti ti-pencil text-sm"></i>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.external-regulations.destroy', $externalRegulation) }}"
                    id="form-delete">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="ina-button ina-button--danger ina-button--md w-full flex items-center justify-center gap-2">
                        <i class="ti ti-trash text-sm"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: PDF viewer --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-700 flex items-center gap-2">
                        <i class="ti ti-eye text-gray-400"></i>
                        Pratinjau Regulasi
                    </p>
                </div>
                <div class="bg-gray-100" style="height: 700px;">
                    <iframe
                        src="{{ route('admin.external-regulations.stream', $externalRegulation) }}"
                        class="w-full h-full border-0"
                        title="{{ $externalRegulation->title }}"
                    ></iframe>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#form-delete').on('submit', function (e) {
        e.preventDefault();
        if (confirm('Hapus regulasi ini? File PDF juga akan dihapus.')) {
            this.submit();
        }
    });
});
</script>
@endpush
