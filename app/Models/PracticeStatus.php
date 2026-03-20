<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeStatus extends Model
{
    protected $table = 'practice_statuses';

    protected $fillable = [
        'code',
        'name',
        'ordine',
        'status',
        'color',
        'is_rejected',
        'is_working',
        'is_completed',
        'is_perfectioned',
        'rejected_month',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_rejected' => 'boolean',
        'is_working' => 'boolean',
        'is_completed' => 'boolean',
        'is_perfectioned' => 'boolean',
        'rejected_month' => 'integer',
    ];
}
