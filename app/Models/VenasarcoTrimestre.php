<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class VenasarcoTrimestre extends Model
{
    protected $table = 'venasarcotrimestre';

    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'competenza',
        'trimestre',
        'produttore',
        'enasarco',
        'montante',
        'contributo',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
