<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObsoleteDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role->name, ['super_admin', 'admin_unit']);
    }

    public function rules(): array
    {
        return [
            'obsolete_reason' => ['required', 'string', 'max:1000'],
            'replaced_by_id'  => ['nullable', 'string', 'exists:documents,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'obsolete_reason.required' => 'Alasan obsolet wajib diisi.',
        ];
    }
}
