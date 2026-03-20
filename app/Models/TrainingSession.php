<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'training_template_id',
        'name',
        'total_hours',
        'trainer_name',
        'start_date',
        'end_date',
        'location',
    ];

    public function trainingTemplate()
    {
        return $this->belongsTo(TrainingTemplate::class);
    }

    public function trainingRecords()
    {
        return $this->hasMany(TrainingRecord::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
