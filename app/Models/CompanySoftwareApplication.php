<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySoftwareApplication extends Model
{
    protected $table = 'company_software_application';

    protected $fillable = [
        'company_id',
        'software_application_id',
        'status',
        'notes',
        'apikey',
        'wallet_balance',
    ];

    protected $casts = [
        'wallet_balance' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function softwareApplication(): BelongsTo
    {
        return $this->belongsTo(SoftwareApplication::class);
    }
}
