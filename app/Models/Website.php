<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'websiteable_type',
        'websiteable_id',
        'company_id',
        'name',
        'domain',
        'type',
        'principal_id',
        'is_active',
        'is_typical',
        'privacy_date',
        'transparency_date',
        'privacy_prior_date',
        'transparency_prior_date',
        'url_privacy',
        'url_cookies',
        'is_footercompilant',
        'url_transparency',
    ];

    protected $casts = [
        'privacy_date' => 'date',
        'transparency_date' => 'date',
        'privacy_prior_date' => 'date',
        'transparency_prior_date' => 'date',
        'is_active' => 'boolean',
        'is_typical' => 'boolean',
        'is_footercompilant' => 'boolean',
    ];

    public function websiteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTypical($query)
    {
        return $query->where('is_typical', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
