<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OamCode extends Model
{
    protected $fillable = [
        'code',
        'fase',
        'name',
    ];

    protected $casts = [
        'fase' => 'string',
    ];
}
