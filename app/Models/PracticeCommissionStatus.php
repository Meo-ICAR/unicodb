<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeCommissionStatus extends Model
{
    protected $table = 'practice_commission_statuses';

    protected $fillable = [
        'status_payment',
        'name',
        'code',
        'is_perfectioned',
        'is_working',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_perfectioned' => 'boolean',
        'is_working' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = false;

    public function practiceCommissions()
    {
        return $this->hasMany(PracticeCommission::class);
    }
}
