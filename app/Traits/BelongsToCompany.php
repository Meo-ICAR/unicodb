<?php

namespace App\Traits;

use App\Models\Company;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if ($tenant = Filament::getTenant()) {
                $builder->where($builder->getModel()->getTable() . '.company_id', $tenant->id);
            }
        });

        static::creating(function ($model) {
            if (($tenant = Filament::getTenant()) && empty($model->company_id)) {
                $model->company_id = $tenant->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
