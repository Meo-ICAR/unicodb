<?php

namespace App\Models;

use App\Models\Rui;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;  // <--- Aggiungi questa
use Spatie\MediaLibrary\InteractsWithMedia;  // <--- Assicurati che ci sia questa

class Principal extends Model
{
    use BelongsToCompany;
    use InteractsWithMedia;  // <--- Usa il trait di Spatie

    protected $fillable = [
        'company_id',
        'name',
        'abi',
        'stipulated_at',
        'dismissed_at',
        'vat_number',
        'vat_name',
        'type',
        'ivass',
        'ivass_at',
        'ivass_name',
        'ivass_section',
        'is_active',
        'mandate_number',
        'start_date',
        'end_date',
        'is_exclusive',
        'status',
        'is_dummy',
        'notes',
        'principal_type',
        'submission_type',
        'website',
        'portalsite',
        'contoCOGE',
        'oam',
        'oam_name',
        'oam_at',
        'numero_iscrizione_rui',
        'is_reported',
    ];

    protected $casts = [
        'stipulated_at' => 'date',
        'dismissed_at' => 'date',
        'oam_at' => 'date',
        'ivass_at' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_exclusive' => 'boolean',
        'is_dummy' => 'boolean',
        'is_reported' => 'boolean',
        'principal_type' => 'string',
    ];

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_rui', 'numero_iscrizione_rui');
    }

    public function checklists()
    {
        // Un agente ha molte checklist (le sue copie assegnate)
        return $this->morphMany(Checklist::class, 'target');
    }

    public function mandates()
    {
        return $this->hasMany(PrincipalMandate::class);
    }

    public function principalScopes()
    {
        return $this->hasMany(PrincipalScope::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(PrincipalEmployee::class);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function purchaseInvoices()
    {
        return $this->morphMany(PurchaseInvoice::class, 'invoiceable');
    }

    public function salesInvoices()
    {
        return $this->morphMany(SalesInvoice::class, 'invoiceable');
    }

    public function principalContacts(): HasMany
    {
        return $this->hasMany(PrincipalContact::class);
    }

    // Helper methods per principal_type
    public function getPrincipalTypeLabelAttribute(): string
    {
        return match ($this->principal_type) {
            'no' => 'Non Specificato',
            'banca' => 'Banca',
            'assicurazione' => 'Compagnia Assicurativa',
            'agente' => 'Agente',
            'agente_captive' => 'Agente Captive',
            default => $this->principal_type,
        };
    }

    public function isBank(): bool
    {
        return $this->principal_type === 'banca';
    }

    public function isInsurance(): bool
    {
        return $this->principal_type === 'assicurazione';
    }

    public function isAgent(): bool
    {
        return $this->principal_type === 'agente';
    }

    public function isCaptiveAgent(): bool
    {
        return $this->principal_type === 'agente_captive';
    }

    public function isFinancialInstitution(): bool
    {
        return in_array($this->principal_type, ['banca', 'assicurazione']);
    }

    public function isAgentType(): bool
    {
        return in_array($this->principal_type, ['agente', 'agente_captive']);
    }

    // Scope per filtrare per tipologia
    public function scopeByPrincipalType($query, string $type)
    {
        return $query->where('principal_type', $type);
    }

    public function scopeBanks($query)
    {
        return $query->where('principal_type', 'banca');
    }

    public function scopeInsurances($query)
    {
        return $query->where('principal_type', 'assicurazione');
    }

    public function websites()
    {
        return $this->morphMany(Website::class, 'websiteable');
    }

    public function scopeAgents($query)
    {
        return $query->whereIn('principal_type', ['agente', 'agente_captive']);
    }
}
