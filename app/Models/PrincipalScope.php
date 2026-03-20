<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrincipalScope extends Model
{
    public $timestamps = false;

    public function practiceScope()
    {
        return $this->belongsTo(PracticeScope::class);
    }
}
