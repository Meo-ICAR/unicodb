<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EmploymentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'privacy_role',
        'purpose',
        'data_subjects',
        'data_categories',
        'retention_period',
        'extra_eu_transfer',
        'security_measures',
        'privacy_data',
        'client_type_id',
    ];

    public function clientType(): BelongsTo
    {
        return $this->belongsTo(ClientType::class);
    }
}
