<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'auditable_type',
        'auditable_id',
        'requester_type',
        'requester_id',
        'remediation_type',
        'title',
        'emails',
        'reference_period',
        'start_date',
        'end_date',
        'status',
        'overall_score',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function auditable()
    {
        return $this->morphTo();
    }

    public function requester()
    {
        return $this->morphTo();
    }

    public function auditItems()
    {
        return $this->hasMany(AuditItem::class);
    }

    // Helper methods per verificare il tipo di auditable
    public function isAgentAudit(): bool
    {
        return $this->auditable_type === 'agent';
    }

    public function isEmployeeAudit(): bool
    {
        return $this->auditable_type === 'employee';
    }

    public function isCompanyBranchAudit(): bool
    {
        return $this->auditable_type === 'company_branch';
    }

    public function isPrincipalAudit(): bool
    {
        return $this->auditable_type === 'principal';
    }

    // Scope per filtrare per tipo di auditable
    public function scopeForAgent($query)
    {
        return $query->where('auditable_type', 'agent');
    }

    public function scopeForEmployee($query)
    {
        return $query->where('auditable_type', 'employee');
    }

    public function scopeForCompanyBranch($query)
    {
        return $query->where('auditable_type', 'company_branch');
    }

    public function scopeForPrincipal($query)
    {
        return $query->where('auditable_type', 'principal');
    }

    // Helper methods per remediation_type
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

    // Scope per filtrare per remediation_type
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
}
