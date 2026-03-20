<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PrincipalEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'principal_id',
        'employee_id',
        'agent_id',
        'usercode',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'num_iscr_intermediario',
        'num_iscr_collaboratori_ii_liv',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        $today = now()->toDateString();
        return $query
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            });
    }

    public function getIsCurrentlyActiveAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->is_active &&
            $this->start_date <= $today &&
            ($this->end_date === null || $this->end_date >= $today);
    }
}
