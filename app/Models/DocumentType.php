<?php

namespace App\Models;

use App\Models\Concerns\BelongsToHospital;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasUuids, BelongsToHospital;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['hospital_id', 'code', 'name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function allowedUnits(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'document_type_units');
    }
}
