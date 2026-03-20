<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionStatus extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'commissions';

    protected $fillable = [
        'tipo',
        'name',
        'company_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the practice commissions for this status
     */
    public function practiceCommissions()
    {
        return $this->hasMany(PracticeCommission::class, 'status_id');
    }

    /**
     * Scope for status
     */
    public function scopeForStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Get formatted creation date
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : null;
    }

    /**
     * Find by status
     */
    public static function findByStatus($status)
    {
        return static::forStatus($status)->first();
    }

    /**
     * Get all available statuses
     */
    public static function getAllStatuses()
    {
        return [
            'istruttoria' => 'Istruttoria',
            'delibera' => 'Delibera',
            'erogata' => 'Erogata',
            'annullata' => 'Annullata',
            'sospesa' => 'Sospesa',
            'approvata' => 'Approvata',
            'respinta' => 'Respinta',
        ];
    }

    /**
     * Get status options for select
     */
    public static function getStatusOptions()
    {
        return collect(static::getAllStatuses())->map(function ($label, $status) {
            return [
                'value' => $status,
                'label' => $label,
            ];
        })->values();
    }
}
