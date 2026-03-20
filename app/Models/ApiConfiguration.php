<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ApiConfiguration extends Model
{
    use BelongsToCompany;

    protected $casts = [
        'api_key' => 'encrypted',
        'api_secret' => 'encrypted',
    ];

    //
    public function softwareApplication()
    {
        return $this->belongsTo(SoftwareApplication::class);
    }

    public function apiLogs()
    {
        return $this->morphMany(ApiLog::class, 'apiLoggable');
    }
}
