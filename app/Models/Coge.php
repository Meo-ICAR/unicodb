<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Coge extends Model
{
    protected $table = 'coges';

    protected $fillable = [
        'company_id',
        'fonte',
        'entrata_uscita',
        'conto_avere',
        'descrizione_avere',
        'conto_dare',
        'descrizione_dare',
        'annotazioni',
        'value_type',
        'value_period',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
