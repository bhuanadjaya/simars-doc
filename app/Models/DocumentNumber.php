<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentNumber extends Model
{
    use HasUuids;

    protected $fillable = ['document_id', 'number', 'sort_order'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
