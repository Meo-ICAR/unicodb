<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PrincipalMandate extends Model
{
    use BelongsToCompany;

    protected $table = 'principal_mandates';

    protected $guarded = [];

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    public function company()
    {
        // Un contatto appartiene alla company del suo Principal
        return $this->principal->company();
    }
}
