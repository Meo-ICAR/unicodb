<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingTemplate extends Model
{
    protected $fillable = [
        'name',
        'category',
        'base_hours',
        'description',
        'is_mandatory',
        'is_active',
    ];
}
