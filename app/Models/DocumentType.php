<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_person',
        'is_signed',
        'is_stored',
        'is_practice',
        'is_monitored',
        'is_template',
        'duration',
        'emitted_by',
        'is_sensible',
        'is_agent',
        'is_principal',
        'is_client',
        'is_practice_target',
        'is_company',
        'is_endmonth',
        'is_AiAbstract',
        'is_AiCheck',
        'AiPattern'
    ];

    protected $casts = [
        'is_endmonth' => 'boolean',
        'is_person' => 'boolean',
        'is_signed' => 'boolean',
        'is_stored' => 'boolean',
        'is_practice' => 'boolean',
        'is_monitored' => 'boolean',
        'is_template' => 'boolean',
        'duration' => 'integer',
        'is_sensible' => 'boolean',
        'is_agent' => 'boolean',
        'is_principal' => 'boolean',
        'is_client' => 'boolean',
        'is_practice_target' => 'boolean',
        'is_company' => 'boolean',
        'is_AiAbstract' => 'boolean',
        'is_AiCheck' => 'boolean',
    ];

    public function scopes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DocumentScope::class, 'document_type_scope');
    }

    /**
     * Scope per documenti relativi alla pratica
     */
    public function scopePracticeRelated($query)
    {
        return $query->where('is_practice', true);
    }

    /**
     * Scope per documenti non relativi alla pratica
     */
    public function scopeGeneral($query)
    {
        return $query->where('is_practice', false);
    }

    /**
     * Scope per documenti per target specifico
     */
    public function scopeForTarget($query, string $target)
    {
        return $query
            ->where("is_{$target}", true)
            ->orWhere('is_company', true);
    }

    /**
     * Scope per agenti
     */
    public function scopeForAgents($query)
    {
        return $query
            ->where('is_agent', true)
            ->orWhere('is_company', true);
    }

    /**
     * Scope per principal
     */
    public function scopeForPrincipals($query)
    {
        return $query
            ->where('is_principal', true)
            ->orWhere('is_company', true);
    }

    /**
     * Scope per client
     */
    public function scopeForClients($query)
    {
        return $query
            ->where('is_client', true)
            ->orWhere('is_company', true);
    }

    /**
     * Scope per practice
     */
    public function scopeForPractices($query)
    {
        return $query
            ->where('is_practice_target', true)
            ->orWhere('is_company', true);
    }

    /**
     * Controlla se il tipo documento è relativo alla pratica
     */
    public function isPracticeRelated(): bool
    {
        return $this->is_practice ?? false;
    }

    /**
     * Controlla se il tipo documento è generale
     */
    public function isGeneral(): bool
    {
        return !$this->isPracticeRelated();
    }
}
