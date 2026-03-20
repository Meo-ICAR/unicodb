<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Vcoge extends Model
{
    use BelongsToCompany;

    protected $table = 'vcoge';

    protected $fillable = [
        'company_id',
        'mese',
        'entrata',
        'uscita',
    ];

    protected $casts = [
        'mese' => 'date',
        'entrata' => 'decimal:2',
        'uscita' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getSaldoAttribute(): float
    {
        return ($this->entrata ?? 0) - ($this->uscita ?? 0);
    }

    public function getMeseFormattatoAttribute(): string
    {
        return $this->mese ? \Carbon\Carbon::parse($this->mese)->format('F Y') : '';
    }
}
