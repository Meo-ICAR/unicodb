<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'ordine',
        'phase',
        'is_phaseclose',
        'name',
        'item_code',
        'question',
        'answer',
        'description',
        'descriptioncheck',
        'annotation',
        'is_required',
        'attach_model',
        'attach_model_id',
        'document_id',
        'n_documents',
        'repeatable_code',
        'depends_on_code',
        'depends_on_value',
        'dependency_type',
        'url_step',
        'url_callback',
        'business_function_id',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_phaseclose' => 'boolean',
        'n_documents' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }

    public function attachedModel(): MorphTo
    {
        return $this->morphTo('attach_model', 'attach_model_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ChecklistDocument::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function businessFunction(): BelongsTo
    {
        return $this->belongsTo(BusinessFunction::class);
    }

    /**
     * Verifica se questo item ha un documento allegato esistente
     */
    public function hasAttachedDocument(): bool
    {
        return !is_null($this->document_id);
    }

    /**
     * Ottiene il documento allegato se presente
     */
    public function getAttachedDocument(): ?Document
    {
        return $this->document;
    }

    /**
     * Ottiene i documento disponibili per il target della checklist
     */
    public function getAvailableDocumentsForTarget(): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->checklist || !$this->checklist->target) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $target = $this->checklist->target;

        return Document::where('documentable_type', get_class($target))
            ->where('documentable_id', $target->id)
            ->with(['documentType'])
            ->get();
    }

    /**
     * Verifica se può allegare un documento esistente
     */
    public function canAttachExistingDocument(): bool
    {
        return $this->n_documents > 0 && $this->hasAttachedDocument() === false;
    }

    public function isRequired(): bool
    {
        return $this->is_required;
    }

    public function hasDocuments(): bool
    {
        return $this->n_documents > 0;
    }

    public function isMultiDocument(): bool
    {
        return $this->n_documents >= 99;
    }

    public function hasDependency(): bool
    {
        return !empty($this->depends_on_code) && !empty($this->depends_on_value);
    }

    public function isRepeatable(): bool
    {
        return !empty($this->repeatable_code);
    }

    public function hasAnswer(): bool
    {
        return !empty($this->answer);
    }

    public function getAttachModelLabelAttribute(): string
    {
        return match ($this->attach_model) {
            'principal' => 'Mandante',
            'agent' => 'Agente',
            'company' => 'Azienda',
            'audit' => 'Audit',
            default => ucfirst($this->attach_model),
        };
    }

    public function getDependencyTypeLabelAttribute(): string
    {
        return match ($this->dependency_type) {
            'show_if' => 'Mostra se',
            'hide_if' => 'Nascondi se',
            default => ucfirst($this->dependency_type),
        };
    }

    public function getDocumentsCountLabelAttribute(): string
    {
        return match ($this->n_documents) {
            0 => 'Nessun documento richiesto',
            1 => '1 documento richiesto',
            99 => 'Documenti multipli',
            default => "{$this->n_documents} documenti richiesti",
        };
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    public function scopeWithDocuments($query)
    {
        return $query->where('n_documents', '>', 0);
    }

    public function scopeWithoutDocuments($query)
    {
        return $query->where('n_documents', 0);
    }

    public function scopeByAttachModel($query, string $model)
    {
        return $query->where('attach_model', $model);
    }

    public function scopeByRepeatableCode($query, string $code)
    {
        return $query->where('repeatable_code', $code);
    }

    public function scopeByDependency($query, string $code)
    {
        return $query->where('depends_on_code', $code);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('ordine');
    }

    public function canBeShownForAnswer(?string $answer = null): bool
    {
        if (!$this->hasDependency()) {
            return true;
        }

        $targetAnswer = $answer ?? $this->getDependencyItemAnswer();

        return match ($this->dependency_type) {
            'show_if' => $targetAnswer === $this->depends_on_value,
            'hide_if' => $targetAnswer !== $this->depends_on_value,
            default => true,
        };
    }

    private function getDependencyItemAnswer(): ?string
    {
        if (!$this->depends_on_code) {
            return null;
        }

        $dependencyItem = $this
            ->checklist
            ->items()
            ->where('item_code', $this->depends_on_code)
            ->first();

        return $dependencyItem?->answer;
    }

    public function getCompletionPercentage(): float
    {
        if (!$this->hasDocuments()) {
            return $this->hasAnswer() ? 100.0 : 0.0;
        }

        $uploadedCount = $this->documents()->count();
        $requiredCount = $this->isMultiDocument() ? 1 : $this->n_documents;

        $answerWeight = $this->hasAnswer() ? 0.3 : 0.0;
        $documentsWeight = min($uploadedCount / $requiredCount, 1.0) * 0.7;

        return ($answerWeight + $documentsWeight) * 100;
    }

    public function isCompleted(): bool
    {
        return $this->getCompletionPercentage() >= 100.0;
    }
}
