<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Checklist extends Model implements HasMedia
{
    use HasFactory, BelongsToCompany, InteractsWithMedia, LogsActivity;

    protected $guarded = [];

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'type',
        'description',
        'principal_id',
        'document_type_id',
        'document_id',
        'is_practice',
        'is_audit',
        'is_template',
        'is_unique',
        'status',
        'checklist_type_id',
        'richiedente',
        'protocollo',
        'duration',
        'user_id',
        'received_at',
        'sended_at',
        'annotation',
        'business_function_id',
    ];

    protected $casts = [
        'type' => 'string',
        'is_practice' => 'boolean',
        'is_audit' => 'boolean',
        'is_template' => 'boolean',
        'is_unique' => 'boolean',
        'status' => 'string',
        'duration' => 'integer',
        'received_at' => 'datetime',
        'sended_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }

    /**
     * Verifica se questa checklist è unica per un target specifico
     */
    public static function isUniqueForTarget($targetType, $targetId, $checklistId = null): bool
    {
        $query = static::where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('is_unique', true);

        if ($checklistId) {
            $query->where('id', '!=', $checklistId);
        }

        return $query->exists();
    }

    /**
     * Verifica se una checklist template può essere creata per questo target
     */
    public static function canCreateForTarget($templateId, $targetType, $targetId): bool
    {
        $template = static::find($templateId);

        if (!$template || !$template->is_unique) {
            return true;  // Non è unica, può essere creata
        }

        // Se è unica, verifica se ne esiste già una per questo target
        return !static::where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('name', $template->name)
            ->where('is_unique', true)
            ->exists();
    }

    /**
     * Verifica se una checklist template può essere assegnata a un target usando il codice
     */
    public static function canAssignTemplate($templateCode, Model $target): bool
    {
        $template = static::where('code', $templateCode)
            ->where('is_template', true)
            ->first();

        if (!$template || !$template->is_unique) {
            return true;  // Non è unica o non esiste, può essere creata
        }

        // Se è unica, verifica se ne esiste già una per questo target
        return !static::where('target_type', get_class($target))
            ->where('target_id', $target->id)
            ->where('name', $template->name)
            ->where('is_unique', true)
            ->exists();
    }

    public function target()
    {
        // Questa checklist a chi appartiene? (Agente, Pratica, ecc.)
        return $this->morphTo();
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function items(): HasMany
    {
        return $this->checklistItems();
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function checklistType(): BelongsTo
    {
        return $this->belongsTo(ChecklistType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function businessFunction(): BelongsTo
    {
        return $this->belongsTo(BusinessFunction::class);
    }

    // Helper methods per document_type
    public function hasDocumentType(): bool
    {
        return !is_null($this->document_type_id);
    }

    public function getDocumentTypeNameAttribute(): string
    {
        return $this->documentType ? $this->documentType->name : 'Non Specificato';
    }

    public function getDocumentTypeCodeAttribute(): string
    {
        return $this->documentType ? $this->documentType->code : '';
    }

    // Scope per filtrare per document_type
    public function scopeByDocumentType($query, $documentTypeId)
    {
        return $query->where('document_type_id', $documentTypeId);
    }

    public function scopeWithDocumentType($query)
    {
        return $query->whereNotNull('document_type_id');
    }

    public function scopeWithoutDocumentType($query)
    {
        return $query->whereNull('document_type_id');
    }

    public function scopeForMonitoredDocuments($query)
    {
        return $query->whereHas('documentType', function ($q) {
            $q->where('is_monitored', true);
        });
    }
}
