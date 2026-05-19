<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalRegulation extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'regulation_number',
        'title',
        'issuing_agency',
        'category',
        'issued_date',
        'effective_date',
        'file_path',
        'status',
        'affected_unit_ids',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_date'      => 'date',
            'effective_date'   => 'date',
            'affected_unit_ids' => 'array',
        ];
    }

    public static array $categoryLabels = [
        'law'                    => 'Undang-Undang',
        'government_regulation'  => 'Peraturan Pemerintah',
        'ministerial_regulation' => 'Peraturan Menteri',
        'ministerial_decree'     => 'Keputusan Menteri',
        'national_standard'      => 'Standar Nasional Indonesia',
        'accreditation_standard' => 'Standar Akreditasi',
        'bpjs_regulation'        => 'Regulasi BPJS',
        'other'                  => 'Lainnya',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::$categoryLabels[$this->category] ?? $this->category;
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
