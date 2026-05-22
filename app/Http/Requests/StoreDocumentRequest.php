<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role->name, ['super_admin', 'admin_unit']);
    }

    public function rules(): array
    {
        return [
            'number'             => ['required', 'string', 'max:100'],
            'title'              => ['required', 'string', 'max:255'],
            'document_type_id'   => ['required', 'string', 'exists:document_types,id'],
            'owner_unit_id'      => ['required', 'string', 'exists:units,id'],
            'source'             => ['required', 'in:internal,external'],
            'effective_date'     => ['nullable', 'date'],
            'description'        => ['nullable', 'string'],
            'tags'               => ['nullable', 'string', 'max:255'],
            'parent_document_id'  => ['nullable', 'string', Rule::exists('documents', 'id')->where(fn ($q) => $q->where('status', 'active')->whereNull('replaced_by_id'))],
            'pdf_file'           => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'docx_file'          => ['nullable', 'file', 'mimes:docx,vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'pdf_file.required' => 'A PDF file is required.',
            'pdf_file.mimes'    => 'The uploaded file must be a valid PDF.',
            'pdf_file.max'      => 'The PDF file must not exceed 20MB.',
            'docx_file.mimes'   => 'The uploaded file must be a valid DOCX.',
            'docx_file.max'     => 'The DOCX file must not exceed 20MB.',
        ];
    }

}
