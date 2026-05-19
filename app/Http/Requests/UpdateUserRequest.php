<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'        => ['required', 'string', 'max:150'],
            'employee_id' => ['nullable', 'string', 'max:30', "unique:users,employee_id,{$userId}"],
            'email'       => ['required', 'email', 'max:150', "unique:users,email,{$userId}"],
            'unit_id'     => ['required', 'string', 'exists:units,id'],
            'role_id'     => ['required', 'string', 'exists:roles,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => 'Nama',
            'employee_id' => 'NIP/ID Pegawai',
            'email'       => 'Email',
            'unit_id'     => 'Unit',
            'role_id'     => 'Role',
        ];
    }
}
