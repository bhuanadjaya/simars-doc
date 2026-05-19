<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'      => ['required', 'string', 'max:20', 'unique:units,code'],
            'name'      => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'string', 'exists:units,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code'      => 'Kode Unit',
            'name'      => 'Nama Unit',
            'parent_id' => 'Unit Induk',
        ];
    }
}
