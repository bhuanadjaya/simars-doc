<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'number', 'title', 'document_type_id', 'owner_unit_id', 'uploaded_by',
        'source', 'revision_number', 'description', 'tags', 'status',
        'effective_date', 'published_at', 'obsolete_date', 'obsolete_reason',
        'obsoleted_by', 'replaced_by_id', 'parent_document_id',
    ];

    protected function casts(): array
    {
        return [
            'effective_date'  => 'date',
            'published_at'    => 'date',
            'obsolete_date'   => 'date',
            'revision_number' => 'integer',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)   { return $query->where('status', 'active'); }
    public function scopeObsolete($query) { return $query->where('status', 'obsolete'); }
    public function scopeDraft($query)    { return $query->where('status', 'draft'); }

    // ── Relationships ─────────────────────────────────────────────────

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function ownerUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'owner_unit_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function obsoleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'obsoleted_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DocumentFile::class);
    }

    public function pdfFile(): HasOne
    {
        return $this->hasOne(DocumentFile::class)->where('file_type', 'pdf');
    }

    public function docxFile(): HasOne
    {
        return $this->hasOne(DocumentFile::class)->where('file_type', 'docx');
    }

    public function parentDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'replaced_by_id');
    }

    public function replaces(): HasOne
    {
        return $this->hasOne(Document::class, 'replaced_by_id');
    }
}
