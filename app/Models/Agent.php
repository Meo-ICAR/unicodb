<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Agent extends Model implements HasMedia
{
    use BelongsToCompany;
    use InteractsWithMedia;

    protected $fillable = [
        'company_id',
        'company_branch_id',
        'coordinated_by_id',
        'coordinated_by_agent_id',
        'name',
        'email',
        'phone',
        'description',
        'supervisor_type',
        'oam',
        'oam_at',
        'oam_dismissed_at',
        'oam_name',
        'ivass',
        'ivass_at',
        'ivass_name',
        'ivass_section',
        'stipulated_at',
        'dismissed_at',
        'type',
        'contribute',
        'contributeFrequency',
        'contributeFrom',
        'remburse',
        'vat_number',
        'vat_name',
        'enasarco',
        'is_active',
        'is_art108',
        'contoCOGE',
        'user_id',
        'numero_iscrizione_rui',
    ];

    protected $casts = [
        'oam_at' => 'date',
        'oam_dismissed_at' => 'date',
        'ivass_at' => 'date',
        'stipulated_at' => 'date',
        'dismissed_at' => 'date',
        'contribute' => 'decimal:2',
        'remburse' => 'decimal:2',
        'contributeFrom' => 'date',
        'contributeFrequency' => 'integer',
        'is_art108' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function checklists()
    {
        // Un agente ha molte checklist (le sue copie assegnate)
        return $this->morphMany(Checklist::class, 'target');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function coordinatedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'coordinated_by_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function companyBranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function coordinatedByAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'coordinated_by_agent_id');
    }

    public function coordinatedAgents()
    {
        return $this->hasMany(Agent::class, 'coordinated_by_agent_id');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function trainingRecords(): MorphMany
    {
        return $this->morphMany(TrainingRecord::class, 'trainable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function purchaseInvoices()
    {
        return $this->morphMany(PurchaseInvoice::class, 'invoiceable');
    }

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_rui', 'numero_iscrizione_rui');
    }

    public function websites()
    {
        return $this->morphMany(Website::class, 'websiteable');
    }
}
