<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExternalRegulationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'regulation_number'  => ['required', 'string', 'max:100'],
            'title'              => ['required', 'string', 'max:255'],
            'issuing_agency'     => ['required', 'string', 'max:150'],
            'category'           => ['required', 'in:law,government_regulation,ministerial_regulation,ministerial_decree,national_standard,accreditation_standard,bpjs_regulation,other'],
            'issued_date'        => ['required', 'date'],
            'effective_date'     => ['required', 'date'],
            'affected_unit_ids'  => ['nullable', 'array'],
            'affected_unit_ids.*'=> ['string', 'exists:units,id'],
            'pdf_file'           => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ];
    }

    public function attributes(): array
    {
        return [
            'regulation_number' => 'Nomor Regulasi',
            'title'             => 'Judul',
            'issuing_agency'    => 'Instansi Penerbit',
            'category'          => 'Kategori',
            'issued_date'       => 'Tanggal Terbit',
            'effective_date'    => 'Tanggal Berlaku',
            'pdf_file'          => 'File PDF',
        ];
    }
}
