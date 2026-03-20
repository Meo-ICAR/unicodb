<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class SoftwareApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider_name',
        'category_id',
        'website_url',
        'api_url',
        'sandbox_url',
        'api_key_url',
        'api_parameters',
        'is_cloud',
        'apikey',
        'wallet_balance',
    ];

    protected $casts = [
        'is_cloud' => 'boolean',
        'wallet_balance' => 'decimal:2',
    ];

    public function apiConfigurations(): HasMany
    {
        return $this->hasMany(ApiConfiguration::class);
    }

    public function softwareMappings(): HasMany
    {
        return $this->hasMany(SoftwareMapping::class);
    }

    public function softwareCategory(): BelongsTo
    {
        return $this->belongsTo(SoftwareCategory::class, 'category_id');
    }

    public function companies(): BelongsToMany
    {
        return $this
            ->belongsToMany(Company::class)
            ->withPivot(['status', 'notes', 'apikey', 'wallet_balance'])
            ->withTimestamps();
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(CompanyWallet::class);
    }
}
