<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class TrainingRecord extends Model
{
    protected $fillable = [
        'training_session_id',
        'trainable_type',
        'trainable_id',
        'status',
        'hours_attended',
        'score',
        'completion_date',
        'certificate_path',
    ];

    public function trainable()
    {
        return $this->morphTo();
    }

    public function employee(): BelongsTo
    {
        return $this
            ->belongsTo(Employee::class, 'trainable_id')
            ->where('trainable_type', Employee::class);
    }

    public function trainingSession()
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
