<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareMapping extends Model
{
    protected $fillable = [
        'software_application_id',
        'mapping_type',
        'name',
        'external_value',
        'internal_id',
        'code',
        'description',
    ];

    protected $casts = [
        'software_application_id' => 'integer',
        'internal_id' => 'integer',
    ];

    public function softwareApplication()
    {
        return $this->belongsTo(SoftwareApplication::class);
    }

    /**
     * Get the internal model based on mapping type
     */
    public function getInternalModel()
    {
        switch ($this->mapping_type) {
            case 'PRACTICE_TYPE':
                return $this->belongsTo(PracticeType::class, 'internal_id');
            case 'PRACTICE_STATUS':
                return $this->belongsTo(PracticeStatus::class, 'internal_id');
            case 'COMMISSION_STATUS':
                return $this->belongsTo(PracticeCommissionStatus::class, 'internal_id');
            case 'CLIENT_TYPE':
                return $this->belongsTo(ClientType::class, 'internal_id');
            case 'BANK_NAME':
                return $this->belongsTo(Principal::class, 'internal_id');
            default:
                return null;
        }
    }

    /**
     * Scope for mapping type
     */
    public function scopeForType($query, $type)
    {
        return $query->where('mapping_type', $type);
    }

    /**
     * Scope for external value
     */
    public function scopeForExternalValue($query, $value)
    {
        return $query->where('external_value', $value);
    }

    /**
     * Find mapping by type and external value
     */
    public static function findByTypeAndExternalValue($type, $externalValue)
    {
        return static::forType($type)->forExternalValue($externalValue)->first();
    }
}
