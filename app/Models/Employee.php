<?php

namespace App\Models;

use App\Models\Rui;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'company_branch_id',
        'coordinated_by_id',
        'name',
        'email',
        'phone',
        'role',
        'department',
        'hire_date',
        'employment_type_id',
        'employee_types',
        'supervisor_type',
        'is_structure',
        'is_ghost',
        'oam',
        'oam_at',
        'oam_dismissed_at',
        'oam_name',
        'numero_iscrizione_rui',
    ];

    protected $casts = [
        'is_structure' => 'boolean',
        'is_ghost' => 'boolean',
        'hire_date' => 'date',
        'oam_at' => 'date',
        'oam_dismissed_at' => 'date',
        'employee_types' => 'string',
        'supervisor_type' => 'string',
    ];

    public function checklists()
    {
        // Un agente ha molte checklist (le sue copie assegnate)
        return $this->morphMany(Checklist::class, 'target');
    }

    public function trainingRecords()
    {
        return $this->morphMany(TrainingRecord::class, 'trainable');
    }

    public function companyBranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function coordinatedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'coordinated_by_id');
    }

    public function coordinatedEmployees()
    {
        return $this->hasMany(Employee::class, 'coordinated_by_id');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_rui', 'numero_iscrizione_rui');
    }

    public function scopeSameBranchCoordinators($query)
    {
        return $query
            ->where('company_branch_id', $this->company_branch_id)
            ->where('id', '!=', $this->id);
    }

    public function scopeStructure($query)
    {
        return $query->where('is_structure', true);
    }

    public function scopeGhost($query)
    {
        return $query->where('is_ghost', true);
    }

    public function scopeRegular($query)
    {
        return $query->where('is_structure', false)->where('is_ghost', false);
    }

    public function getEmployeeTypeAttribute(): string
    {
        if ($this->is_structure) {
            return 'Struttura';
        }
        if ($this->is_ghost) {
            return 'Prestato';
        }
        return 'Regolare';
    }

    // Helper methods per employee_types
    public function getEmployeeTypesLabelAttribute(): string
    {
        return match ($this->employee_types) {
            'dipendente' => 'Dipendente',
            'collaboratore' => 'Collaboratore',
            'stagista' => 'Stagista',
            'consulente' => 'Consulente',
            'amministratore' => 'Amministratore',
            default => $this->employee_types,
        };
    }

    public function getSupervisorTypeLabelAttribute(): string
    {
        return match ($this->supervisor_type) {
            'no' => 'Non Supervisore',
            'si' => 'Supervisore',
            'filiale' => 'Supervisore di Filiale',
            default => $this->supervisor_type,
        };
    }

    public function isEmployee(): bool
    {
        return $this->employee_types === 'dipendente';
    }

    public function isCollaborator(): bool
    {
        return $this->employee_types === 'collaboratore';
    }

    public function isIntern(): bool
    {
        return $this->employee_types === 'stagista';
    }

    public function isConsultant(): bool
    {
        return $this->employee_types === 'consulente';
    }

    public function isAdministrator(): bool
    {
        return $this->employee_types === 'amministratore';
    }

    public function isSupervisor(): bool
    {
        return in_array($this->supervisor_type, ['si', 'filiale']);
    }

    public function isBranchSupervisor(): bool
    {
        return $this->supervisor_type === 'filiale';
    }

    // Scope per filtrare per tipologia
    public function scopeByEmployeeType($query, string $type)
    {
        return $query->where('employee_types', $type);
    }

    public function scopeBySupervisorType($query, string $type)
    {
        return $query->where('supervisor_type', $type);
    }

    public function scopeEmployees($query)
    {
        return $query->where('employee_types', 'dipendente');
    }

    public function scopeCollaborators($query)
    {
        return $query->where('employee_types', 'collaboratore');
    }

    public function scopeInterns($query)
    {
        return $query->where('employee_types', 'stagista');
    }

    public function scopeConsultants($query)
    {
        return $query->where('employee_types', 'consulente');
    }

    public function scopeAdministrators($query)
    {
        return $query->where('employee_types', 'amministratore');
    }

    public function scopeSupervisors($query)
    {
        return $query->whereIn('supervisor_type', ['si', 'filiale']);
    }

    public function scopeBranchSupervisors($query)
    {
        return $query->where('supervisor_type', 'filiale');
    }

    protected function full_name(): string
    {
        return $this->name . ' ' . $this->first_name;
    }
}
