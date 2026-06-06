<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToHospital
{
    protected static function bootBelongsToHospital(): void
    {
        static::addGlobalScope('hospital', function (Builder $query) {
            $user = auth()->user();

            // Bypass: unauthenticated atau system_admin (hospital_id = null)
            if (! $user || ! $user->hospital_id) {
                return;
            }

            $query->where(static::qualifyHospitalColumn(), $user->hospital_id);
        });

        // Otomatis set hospital_id saat create jika belum diisi
        static::creating(function ($model) {
            if (empty($model->hospital_id)) {
                $user = auth()->user();
                if ($user && $user->hospital_id) {
                    $model->hospital_id = $user->hospital_id;
                }
            }
        });
    }

    protected static function qualifyHospitalColumn(): string
    {
        return (new static)->getTable() . '.hospital_id';
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Hospital::class);
    }
}
