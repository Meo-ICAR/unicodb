<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class VenasarcoTot extends Model
{
    protected $table = 'venasarcotot';

    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'produttore',
        'montante',
        'contributo',
        'X',
        'imposta',
        'firr',
        'competenza',
        'enasarco',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
