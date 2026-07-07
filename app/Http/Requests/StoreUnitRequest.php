<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'      => [
                'required', 'string', 'max:20',
                Rule::unique('units', 'code')->where('hospital_id', auth()->user()->hospital_id),
            ],
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
