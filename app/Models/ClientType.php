<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_person',
        'is_company',
        'privacy_data',
        'privacy_role',
        'purpose',
        'data_subjects',
        'data_categories',
        'retention_period',
        'extra_eu_transfer',
        'security_measures',
    ];

    public function employmentTypes(): HasMany
    {
        return $this->hasMany(EmploymentType::class);
    }
}
