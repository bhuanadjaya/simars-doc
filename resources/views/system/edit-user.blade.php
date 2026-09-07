@extends('layouts.app')

@section('title', 'Edit User — System Admin')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('system.users') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-3">
            <i class="ti ti-arrow-left text-sm"></i> Kembali ke Daftar User
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $user->name }} · {{ $user->email }}</p>
    </div>

    @if ($errors->any())
        <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-xl">
            <i class="ti ti-alert-circle text-red-500 shrink-0 mt-0.5"></i>
            <ul class="text-sm text-red-700 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('system.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                {{-- Info readonly --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="ina-text-field">
                        <label class="ina-text-field__label">Nama</label>
                        <div class="ina-text-field__wrapper">
                            <input type="text" class="ina-text-field__input bg-gray-50 text-gray-500"
                                value="{{ $user->name }}" disabled>
                        </div>
                    </div>
                    <div class="ina-text-field">
                        <label class="ina-text-field__label">Email</label>
                        <div class="ina-text-field__wrapper">
                            <input type="text" class="ina-text-field__input bg-gray-50 text-gray-500"
                                value="{{ $user->email }}" disabled>
                        </div>
                    </div>
                </div>

                {{-- Role --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="role_id">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <div class="ina-text-field__wrapper {{ $errors->has('role_id') ? 'ina-text-field__wrapper--status-error' : '' }}">
                        <select id="role_id" name="role_id" class="ina-text-field__input">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Hospital --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="hospital_id">Rumah Sakit</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('hospital_id') ? 'ina-text-field__wrapper--status-error' : '' }}">
                        <select id="hospital_id" name="hospital_id" class="ina-text-field__input">
                            <option value="">— Tidak ada (System Admin) —</option>
                            @foreach ($hospitals as $hospital)
                                <option value="{{ $hospital->id }}"
                                    {{ old('hospital_id', $user->hospital_id) == $hospital->id ? 'selected' : '' }}>
                                    {{ $hospital->code }} — {{ $hospital->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('hospital_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Unit --}}
                <div class="ina-text-field">
                    <label class="ina-text-field__label" for="unit_id">Unit</label>
                    <div class="ina-text-field__wrapper {{ $errors->has('unit_id') ? 'ina-text-field__wrapper--status-error' : '' }}">
                        <select id="unit_id" name="unit_id" class="ina-text-field__input">
                            <option value="">— Pilih unit —</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id', $user->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->code }} — {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('unit_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="ina-button ina-button--primary ina-button--md">
                    Simpan Perubahan
                </button>
                <a href="{{ route('system.users') }}" class="ina-button ina-button--secondary ina-button--md">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#hospital_id').on('change', function () {
        var hospitalId = $(this).val();
        var $unitSelect = $('#unit_id');

        $unitSelect.html('<option value="">— Memuat unit... —</option>').prop('disabled', true);

        if (!hospitalId) {
            $unitSelect.html('<option value="">— Pilih unit —</option>').prop('disabled', false);
            return;
        }

        $.getJSON('{{ route("system.hospitals.units", ":id") }}'.replace(':id', hospitalId), function (units) {
            var options = '<option value="">— Pilih unit —</option>';
            $.each(units, function (i, unit) {
                options += '<option value="' + unit.id + '">' + unit.code + ' — ' + unit.name + '</option>';
            });
            $unitSelect.html(options).prop('disabled', false);
        });
    });
});
</script>
@endpush
