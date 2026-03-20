<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Client extends Model implements HasMedia
{
    use BelongsToCompany, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'company_id',
        'is_company',
        'is_client',
        'is_lead',
        'leadsource_id',
        'acquired_at',
        'is_person',
        'name',
        'first_name',
        'tax_code',
        'email',
        'phone',
        'is_pep',
        'client_type_id',
        'is_sanctioned',
        'is_remote_interaction',
        'general_consent_at',
        'privacy_policy_read_at',
        'consent_special_categories_at',
        'consent_sic_at',
        'consent_marketing_at',
        'consent_profiling_at',
        'status',
        'privacy_consent',
        'subfornitori',
        'is_requiredApprovation',
        'is_approved',
        'is_anonymous',
        'blacklist_at',
        'blacklisted_by',
        'salary',
        'salary_quote',
        'is_art108',
    ];

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'leadsource_id');
    }

    public function leadSourceClients()
    {
        return $this->hasMany(Client::class, 'leadsource_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/jpg',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])
            ->useDisk('public');

        $this
            ->addMediaCollection('photos')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/webp',
                'image/svg+xml',
            ])
            ->useDisk('public')
            ->registerMediaConversions(function ($media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150)
                    ->sharpen(10)
                    ->crop('crop-center');

                $this
                    ->addMediaConversion('medium')
                    ->width(300)
                    ->height(300)
                    ->sharpen(10);

                $this
                    ->addMediaConversion('large')
                    ->width(800)
                    ->height(600)
                    ->sharpen(10);
            });
    }

    public function checklists()
    {
        // Un agente ha molte checklist (le sue copie assegnate)
        return $this->morphMany(Checklist::class, 'target');
    }

    public function purchaseInvoices()
    {
        return $this->morphMany(PurchaseInvoice::class, 'invoiceable');
    }

    public function salesInvoices()
    {
        return $this->morphMany(SalesInvoice::class, 'invoiceable');
    }

    public function members()
    {
        return $this
            ->belongsToMany(Client::class, 'client_relations', 'company_id', 'client_id')
            ->withPivot('shares_percentage', 'is_titolare', 'client_type_id', 'data_inizio_ruolo', 'data_fine_ruolo')
            ->withTimestamps();
    }

    public function companyRelations()
    {
        return $this->hasMany(ClientRelation::class, 'company_id');
    }

    public function personRelations()
    {
        return $this->hasMany(ClientRelation::class, 'client_id');
    }

    public function clientMandates(): HasMany
    {
        return $this->hasMany(ClientMandate::class);
    }

    public function activeMandates(): HasMany
    {
        return $this->clientMandates()->where('stato', 'attivo');
    }

    public function hasActiveMandates(): bool
    {
        return $this->activeMandates()->exists();
    }

    public function getLatestMandate()
    {
        return $this->clientMandates()->latest()->first();
    }

    public function getTotalMandateAmount(): float
    {
        return $this
            ->clientMandates()
            ->whereNotNull('importo_richiesto_mandato')
            ->sum('importo_richiesto_mandato');
    }

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }

    protected function casts(): array
    {
        return [
            'is_person' => 'boolean',
            'is_pep' => 'boolean',
            'is_sanctioned' => 'boolean',
            'is_remote_interaction' => 'boolean',
            'is_company' => 'boolean',
            'is_client' => 'boolean',
            'is_lead' => 'boolean',
            'privacy_consent' => 'boolean',
            'is_requiredApprovation' => 'boolean',
            'is_approved' => 'boolean',
            'is_anonymous' => 'boolean',
            'is_art108' => 'boolean',
            'general_consent_at' => 'datetime',
            'privacy_policy_read_at' => 'datetime',
            'consent_special_categories_at' => 'datetime',
            'consent_sic_at' => 'datetime',
            'consent_marketing_at' => 'datetime',
            'consent_profiling_at' => 'datetime',
            'acquired_at' => 'datetime',
            'blacklist_at' => 'datetime',
            'salary' => 'decimal:2',
            'salary_quote' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected function fullName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_person ? "{$this->name} {$this->first_name}" : $this->name,
        );
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photos') ?? asset('images/default-avatar.png');
    }

    public function getPhotoThumbUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photos', 'thumb') ?? asset('images/default-avatar.png');
    }

    public function getPhotoMediumUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photos', 'medium') ?? asset('images/default-avatar.png');
    }

    public function getPhotoLargeUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photos', 'large') ?? asset('images/default-avatar.png');
    }

    public function hasPhoto(): bool
    {
        return $this->hasMedia('photos');
    }

    public function websites()
    {
        return $this->morphMany(Website::class, 'websiteable');
    }
}
