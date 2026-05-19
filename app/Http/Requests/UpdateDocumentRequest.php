<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role->name, ['super_admin', 'admin_unit']);
    }

    public function rules(): array
    {
        return [
            'number'           => ['required', 'string', 'max:100'],
            'title'            => ['required', 'string', 'max:255'],
            'document_type_id' => ['required', 'string', 'exists:document_types,id'],
            'source'           => ['required', 'in:internal,external'],
            'effective_date'   => ['nullable', 'date'],
            'description'      => ['nullable', 'string'],
            'tags'             => ['nullable', 'string', 'max:255'],
            'pdf_file'         => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'docx_file'        => ['nullable', 'file', 'mimes:docx,vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'pdf_file.mimes'  => 'The uploaded file must be a valid PDF.',
            'pdf_file.max'    => 'The PDF file must not exceed 20MB.',
            'docx_file.mimes' => 'The uploaded file must be a valid DOCX.',
            'docx_file.max'   => 'The DOCX file must not exceed 20MB.',
        ];
    }
}
