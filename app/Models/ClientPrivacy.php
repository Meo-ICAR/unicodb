<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ClientPrivacy extends Model
{
    use BelongsToCompany;
    use InteractsWithMedia;  // <--- Usa il trait di Spatie

    protected $table = 'client_privacies';

    protected $fillable = [
        'company_id',
        'client_id',
        'request_type',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Relazione con la Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function checklist()
    {
        // Un client privacy ha una sola checklist
        return $this->morphOne(Checklist::class, 'target');
    }
}
