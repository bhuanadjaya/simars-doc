<?php

namespace App\Http\Requests;

use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $unit = $this->route('unit');

        return [
            'code'      => [
                'required', 'string', 'max:20',
                Rule::unique('units', 'code')
                    ->where('hospital_id', auth()->user()->hospital_id)
                    ->ignore($unit->id),
            ],
            'name'      => ['required', 'string', 'max:150'],
            'parent_id' => [
                'nullable', 'string', 'exists:units,id',
                fn ($attribute, $value, $fail) => $this->validateNoCycle($unit, $value, $fail),
            ],
        ];
    }

    /**
     * Unit tidak boleh menjadi induk dirinya sendiri, dan induk baru tidak boleh
     * merupakan turunan dari unit ini — keduanya membuat hierarki melingkar.
     */
    private function validateNoCycle(Unit $unit, mixed $parentId, callable $fail): void
    {
        if ($parentId === $unit->id) {
            $fail('Unit tidak dapat dijadikan induk bagi dirinya sendiri.');

            return;
        }

        $ancestorId = $parentId;
        $depth      = 0;

        while ($ancestorId && $depth++ < 50) {
            if ($ancestorId === $unit->id) {
                $fail('Unit Induk tidak valid karena merupakan turunan dari unit ini.');

                return;
            }

            $ancestorId = Unit::where('id', $ancestorId)->value('parent_id');
        }
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
