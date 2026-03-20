<?php

namespace App\Models;

use App\Models\Rui;
use App\Models\RuiCollaboratori;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;  // <--- Deve esserci
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends Model implements HasCurrentTenantLabel, HasMedia
{
    use HasUuids, InteractsWithMedia, HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'vat_number',
        'vat_name',
        'oam',
        'oam_at',
        'oam_name',
        'numero_iscrizione_rui',
        'company_type_id',
        'page_header',
        'page_footer',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_email',
        'smtp_from_name',
        'smtp_enabled',
        'smtp_verify_ssl',
        'sponsor'
    ];

    protected $casts = [
        'oam_at' => 'date',
        'smtp_enabled' => 'boolean',
        'smtp_verify_ssl' => 'boolean',
        'smtp_port' => 'integer',
    ];

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_rui', 'numero_iscrizione_rui');
    }

    public function ruiSedi()
    {
        return $this->belongsTo(RuiSedi::class, 'numero_iscrizione_rui', 'numero_iscrizione_int');
    }

    /**
     * Update company address from RUI sede data
     */
    public function updateAddressFromRuiSedi(): void
    {
        if (!$this->ruiSedi) {
            return;
        }

        $this->update([
            'address' => $this->ruiSedi->indirizzo_sede,
            'city' => $this->ruiSedi->comune_sede,
            'province' => $this->ruiSedi->provincia_sede,
            'postal_code' => $this->ruiSedi->cap_sede,
        ]);
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function getCurrentTenantLabel(): string
    {
        return 'Company';
    }

    public function checklists()
    {
        // Un agente ha molte checklist (le sue copie assegnate)
        return $this->morphMany(Checklist::class, 'target');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/svg+xml',
                'image/webp',
            ])
            ->useDisk('public')
            ->singleFile()
            ->registerMediaConversions(function ($media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(200)
                    ->height(200)
                    ->sharpen(10);
            });
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo') ?? asset('images/default-logo.png');
    }

    public function getLogoThumbUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo', 'thumb') ?? asset('images/default-logo.png');
    }

    public function branches()
    {
        return $this->hasMany(CompanyBranch::class);
    }

    public function websites()
    {
        return $this->morphMany(Website::class, 'websiteable');
    }

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class);
    }

    public function softwareApplications()
    {
        return $this
            ->belongsToMany(SoftwareApplication::class)
            ->withPivot(['status', 'notes', 'apikey', 'wallet_balance'])
            ->withTimestamps();
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function trainingRecords(): MorphMany
    {
        return $this->morphMany(TrainingRecord::class, 'trainable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function companyClients(): HasMany
    {
        return $this->hasMany(CompanyClient::class);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(CompanyWallet::class);
    }

    public function companyFunctions(): HasMany
    {
        return $this->hasMany(CompanyFunction::class);
    }

    public function principals(): HasMany
    {
        return $this->hasMany(Principal::class);
    }

    public function apiUsageLogs(): HasMany
    {
        return $this->hasMany(CompanyApiUsageLog::class);
    }

    public function ruiCollaboratori()
    {
        return $this->hasMany(
            RuiCollaboratori::class,
            'num_iscr_collaboratori_i_liv',
            'numero_iscrizione_rui'
        );
    }

    public function ruiCollaboratoriPrincipal()
    {
        return $this
            ->ruiCollaboratori()
            ->where(function ($query) {
                $query
                    ->whereNull('num_iscr_collaboratori_ii_liv')
                    ->orWhere('num_iscr_collaboratori_ii_liv', '');
            });
    }

    public function ruiCollaboratoriEmployee()
    {
        return $this
            ->ruiCollaboratori()
            ->whereNotNull('num_iscr_collaboratori_ii_liv');
    }

    public function companySenders(): HasMany
    {
        return $this->hasMany(CompanySender::class);
    }
}
