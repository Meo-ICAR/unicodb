<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChecklistDocument extends Model
{
    protected $fillable = [
        'practice_scope_id',
        'document_type_id',
        'principal_id',
        'is_required',
        'description',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function practiceScope(): BelongsTo
    {
        return $this->belongsTo(PracticeScope::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }
}
