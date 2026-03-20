<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class BusinessFunction extends Model
{
    use HasFactory;

    protected $table = 'business_functions';

    protected $fillable = [
        'code',
        'macro_area',
        'name',
        'type',
        'description',
        'outsourcable_status',
        'managed_by_code',
        'mission',
        'responsibility',
    ];

    protected $casts = [
        'outsourcable_status' => 'string',
    ];

    // Relazioni
    public function remediations(): HasMany
    {
        return $this->hasMany(Remediation::class, 'business_function_id');
    }

    public function companyFunctions(): HasMany
    {
        return $this->hasMany(CompanyFunction::class, 'function_id');
    }

    public function ropaEntries(): HasMany
    {
        return $this->hasMany(RopaEntry::class, 'function_id');
    }

    public function managingFunction()
    {
        return $this->belongsTo(BusinessFunction::class, 'managed_by_code', 'code');
    }

    public function managedFunctions()
    {
        return $this->hasMany(BusinessFunction::class, 'managed_by_code', 'code');
    }

    // Helper methods
    public function getMacroAreaLabelAttribute(): string
    {
        return $this->macro_area;
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type;
    }

    public function getOutsourcableStatusLabelAttribute(): string
    {
        return match ($this->outsourcable_status) {
            'yes' => 'Sì',
            'no' => 'No',
            'partial' => 'Parziale',
            default => $this->outsourcable_status,
        };
    }

    public function isOutsourcable(): bool
    {
        return $this->outsourcable_status === 'yes';
    }

    public function isPartiallyOutsourcable(): bool
    {
        return $this->outsourcable_status === 'partial';
    }

    public function isNotOutsourcable(): bool
    {
        return $this->outsourcable_status === 'no';
    }

    public function isStrategic(): bool
    {
        return $this->type === 'Strategica';
    }

    public function isOperational(): bool
    {
        return $this->type === 'Operativa';
    }

    public function isSupport(): bool
    {
        return $this->type === 'Supporto';
    }

    public function isControl(): bool
    {
        return $this->type === 'Controllo';
    }

    // Scope per filtrare
    public function scopeByMacroArea($query, string $macroArea)
    {
        return $query->where('macro_area', $macroArea);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOutsourcable($query)
    {
        return $query->where('outsourcable_status', 'yes');
    }

    public function scopeNotOutsourcable($query)
    {
        return $query->where('outsourcable_status', 'no');
    }

    public function scopePartiallyOutsourcable($query)
    {
        return $query->where('outsourcable_status', 'partial');
    }

    public function scopeStrategic($query)
    {
        return $query->where('type', 'Strategica');
    }

    public function scopeOperational($query)
    {
        return $query->where('type', 'Operativa');
    }

    public function scopeSupport($query)
    {
        return $query->where('type', 'Supporto');
    }

    public function scopeControl($query)
    {
        return $query->where('type', 'Controllo');
    }
}
