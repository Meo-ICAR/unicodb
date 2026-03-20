<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firr extends Model
{
    protected $table = 'firrs';

    protected $fillable = [
        'minimo',
        'massimo',
        'aliquota',
        'competenza',
        'enasarco',
    ];
}
