<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrincipalContact extends Model
{
    //
    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    public function company()
    {
        // Usiamo l'operatore null-safe per evitare il crash
        // Se il principal è null, restituiamo comunque la relazione ma vuota
        return $this->principal?->company() ?? $this->belongsTo(Company::class, 'company_id');
    }
}
