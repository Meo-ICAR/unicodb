<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity as SpatieActivityLog;

class ActivityLog extends SpatieActivityLog
{
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
