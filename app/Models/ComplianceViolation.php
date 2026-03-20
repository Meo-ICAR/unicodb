<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class ComplianceViolation extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'user_id',
        'violatable_type',
        'violatable_id',
        'violation_type',
        'severity',
        'description',
        'ip_address',
        'user_agent',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function violatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getSeverityBadgeAttribute(): string
    {
        return match ($this->severity) {
            'basso' => 'success',
            'medio' => 'warning',
            'alto' => 'danger',
            'critico' => 'danger',
            default => 'secondary'
        };
    }

    public function getViolationTypeLabelAttribute(): string
    {
        return match ($this->violation_type) {
            'accesso_non_autorizzato' => 'Accesso Non Autorizzato',
            'kyc_scaduto' => 'KYC Scaduto',
            'forzatura_stato' => 'Forzatura Stato',
            'data_breach' => 'Data Breach',
            default => ucfirst(str_replace('_', ' ', $this->violation_type))
        };
    }

    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            'basso' => 'Basso',
            'medio' => 'Medio',
            'alto' => 'Alto',
            'critico' => 'Critico',
            default => ucfirst($this->severity)
        };
    }

    public function isResolved(): bool
    {
        return !is_null($this->resolved_at);
    }

    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('violation_type', $type);
    }
}
