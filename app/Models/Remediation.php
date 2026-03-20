<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Remediation extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_id',
        'business_function_id',
        'remediation_type',
        'name',
        'description',
        'timeframe_hours',
        'timeframe_desc',
    ];

    protected $casts = [
        'timeframe_hours' => 'integer',
    ];

    // Relazioni
    public function auditItem(): BelongsTo
    {
        return $this->belongsTo(AuditItem::class);
    }

    public function businessFunction(): BelongsTo
    {
        return $this->belongsTo(BusinessFunction::class);
    }

    public function scopes(): BelongsToMany
    {
        return $this->belongsToMany(Scope::class);
    }

    // Helper methods
    public function getRemediationTypeLabelAttribute(): string
    {
        return match ($this->remediation_type) {
            'AML' => 'Antiriciclaggio',
            'Gestione Reclami' => 'Gestione Reclami',
            'Monitoraggio Rete' => 'Monitoraggio Rete',
            'Privacy' => 'Privacy',
            'Trasparenza' => 'Trasparenza',
            'Assetto Organizzativo' => 'Assetto Organizzativo',
            default => $this->remediation_type ?? 'Non Specificato',
        };
    }

    public function isAMLRemediation(): bool
    {
        return $this->remediation_type === 'AML';
    }

    public function isComplaintRemediation(): bool
    {
        return $this->remediation_type === 'Gestione Reclami';
    }

    public function isNetworkRemediation(): bool
    {
        return $this->remediation_type === 'Monitoraggio Rete';
    }

    public function isPrivacyRemediation(): bool
    {
        return $this->remediation_type === 'Privacy';
    }

    public function isTransparencyRemediation(): bool
    {
        return $this->remediation_type === 'Trasparenza';
    }

    public function isOrganizationalRemediation(): bool
    {
        return $this->remediation_type === 'Assetto Organizzativo';
    }

    public function getTimeframeFormattedAttribute(): string
    {
        if ($this->timeframe_hours) {
            if ($this->timeframe_hours < 24) {
                return "{$this->timeframe_hours} ore";
            } elseif ($this->timeframe_hours < 168) {
                $days = round($this->timeframe_hours / 24);
                return "{$days} giorni";
            } elseif ($this->timeframe_hours < 720) {
                $weeks = round($this->timeframe_hours / 168);
                return "{$weeks} settimane";
            } else {
                $months = round($this->timeframe_hours / 720);
                return "{$months} mesi";
            }
        }
        return $this->timeframe_desc ?? 'Non specificato';
    }

    public function isUrgent(): bool
    {
        return $this->timeframe_hours && $this->timeframe_hours <= 48;
    }

    public function isHighPriority(): bool
    {
        return $this->timeframe_hours && $this->timeframe_hours <= 168;
    }

    // Scope per filtrare
    public function scopeByRemediationType($query, string $type)
    {
        return $query->where('remediation_type', $type);
    }

    public function scopeAML($query)
    {
        return $query->where('remediation_type', 'AML');
    }

    public function scopeComplaints($query)
    {
        return $query->where('remediation_type', 'Gestione Reclami');
    }

    public function scopeNetwork($query)
    {
        return $query->where('remediation_type', 'Monitoraggio Rete');
    }

    public function scopePrivacy($query)
    {
        return $query->where('remediation_type', 'Privacy');
    }

    public function scopeTransparency($query)
    {
        return $query->where('remediation_type', 'Trasparenza');
    }

    public function scopeOrganizational($query)
    {
        return $query->where('remediation_type', 'Assetto Organizzativo');
    }

    public function scopeUrgent($query)
    {
        return $query->where('timeframe_hours', '<=', 48);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('timeframe_hours', '<=', 168);
    }

    public function scopeByBusinessFunction($query, int $functionId)
    {
        return $query->where('business_function_id', $functionId);
    }

    public function scopeWithBusinessFunction($query)
    {
        return $query->whereNotNull('business_function_id');
    }

    public function scopeWithoutBusinessFunction($query)
    {
        return $query->whereNull('business_function_id');
    }
}
