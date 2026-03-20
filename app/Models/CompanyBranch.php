<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'is_main_office',
        'manager_first_name',
        'manager_last_name',
        'manager_tax_code',
    ];

    protected $casts = [
        'is_main_office' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    // Boot method per gestire il salvataggio automatico dell'indirizzo
    protected static function booted()
    {
        static::saved(function ($companyBranch) {
            // Se ci sono dati dell'indirizzo nel request, salvali
            if (request()->has('address')) {
                $addressData = request()->input('address');

                $address = $companyBranch->address ?? new Address();
                $address->addressable_type = CompanyBranch::class;
                $address->addressable_id = $companyBranch->id;
                $address->fill($addressData);
                $address->save();
            }
        });
    }
}
