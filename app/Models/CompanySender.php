<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySender extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'eventgroup',
        'eventcode',
        'emails',
        'name',
        'email',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope per sender attivi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per gruppo eventi
     */
    public function scopeForEventGroup($query, $eventGroup)
    {
        return $query->where('eventgroup', $eventGroup);
    }

    /**
     * Scope per codice evento
     */
    public function scopeForEventCode($query, $eventCode)
    {
        return $query->where('eventcode', $eventCode);
    }

    /**
     * Ottieni le email CC come array
     */
    public function getCcEmailsAttribute(): array
    {
        if (empty($this->emails)) {
            return [];
        }

        return array_map('trim', explode(',', $this->emails));
    }
}
