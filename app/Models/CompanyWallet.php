<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CompanyWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'software_application_id',
        'credit',
        'start_date',
        'trial_date',
        'is_active',
        'name',
        'description',
    ];

    protected $casts = [
        'company_id' => 'string',
        'credit' => 'decimal:2',
        'start_date' => 'date',
        'trial_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function softwareApplication(): BelongsTo
    {
        return $this->belongsTo(SoftwareApplication::class);
    }

    // Scopes utili
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeTrial($query)
    {
        return $query
            ->whereNotNull('trial_date')
            ->where('trial_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query
            ->whereNotNull('trial_date')
            ->where('trial_date', '<', now());
    }

    public function scopeWithCredit($query)
    {
        return $query->where('credit', '>', 0);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeBySoftware($query, $softwareId)
    {
        return $query->where('software_application_id', $softwareId);
    }

    // Accessors
    public function getFormattedCreditAttribute(): string
    {
        return '€' . number_format($this->credit, 2, ',', '.');
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inattivo';
        }

        if ($this->isTrial()) {
            return 'Trial';
        }

        return 'Attivo';
    }

    public function getRemainingTrialDaysAttribute(): ?int
    {
        if (!$this->trial_date) {
            return null;
        }

        $remaining = now()->diffInDays($this->trial_date, false);
        return $remaining >= 0 ? $remaining : 0;
    }

    // Methods
    public function isTrial(): bool
    {
        return $this->trial_date && $this->trial_date >= now();
    }

    public function isExpired(): bool
    {
        return $this->trial_date && $this->trial_date < now();
    }

    public function hasCredit(): bool
    {
        return $this->credit > 0;
    }

    public function addCredit(float $amount): void
    {
        $this->credit += $amount;
        $this->save();
    }

    public function consumeCredit(float $amount): bool
    {
        if ($this->credit >= $amount) {
            $this->credit -= $amount;
            $this->save();
            return true;
        }

        return false;
    }

    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }
}
