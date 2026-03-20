<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class AuditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_id',
        'is_template',
        'name',
        'description',
        'audit_phase',
        'code',
        'business_function_id',
    ];

    protected $casts = [
        'is_template' => 'boolean',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function businessFunction(): BelongsTo
    {
        return $this->belongsTo(BusinessFunction::class);
    }

    // Helper methods
    public function isTemplate(): bool
    {
        return $this->is_template;
    }

    public function getAuditPhaseLabelAttribute(): string
    {
        return match ($this->audit_phase) {
            'preparazione' => 'Preparazione',
            'esecuzione' => 'Esecuzione',
            'follow_up' => 'Follow-up',
            'report' => 'Report',
            default => $this->audit_phase ?? 'Non Specificata',
        };
    }

    // Scope per filtrare
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeNotTemplates($query)
    {
        return $query->where('is_template', false);
    }

    public function scopeByPhase($query, string $phase)
    {
        return $query->where('audit_phase', $phase);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
