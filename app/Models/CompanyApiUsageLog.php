<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CompanyApiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'service_type',
        'software_cost',
        'charged_credits',
        'status',
        'request_data',
        'response_data',
        'error_message',
        'response_time_ms',
    ];

    protected $casts = [
        'company_id' => 'string',
        'software_cost' => 'decimal:4',
        'charged_credits' => 'decimal:2',
        'request_data' => 'array',
        'response_data' => 'array',
        'response_time_ms' => 'integer',
    ];

    // Costanti per stati
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_TIMEOUT = 'timeout';
    // Costanti per tipi di servizio
    const SERVICE_OCR = 'OCR';
    const SERVICE_SIGNATURE = 'SIGNATURE';
    const SERVICE_TRANSLATION = 'TRANSLATION';
    const SERVICE_VALIDATION = 'VALIDATION';
    const SERVICE_ANALYSIS = 'ANALYSIS';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes utili
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByService($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeWithCost($query)
    {
        return $query->where('charged_credits', '>', 0);
    }

    public function scopeSlowResponse($query, $thresholdMs = 5000)
    {
        return $query->where('response_time_ms', '>', $thresholdMs);
    }

    // Accessors
    public function getFormattedSoftwareCostAttribute(): string
    {
        return '€' . number_format($this->software_cost, 4, ',', '.');
    }

    public function getFormattedChargedCreditsAttribute(): string
    {
        return '€' . number_format($this->charged_credits, 2, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SUCCESS => 'Successo',
            self::STATUS_FAILED => 'Fallito',
            self::STATUS_PENDING => 'In attesa',
            self::STATUS_TIMEOUT => 'Timeout',
            default => ucfirst($this->status),
        };
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return match ($this->service_type) {
            self::SERVICE_OCR => 'OCR',
            self::SERVICE_SIGNATURE => 'Firma Digitale',
            self::SERVICE_TRANSLATION => 'Traduzione',
            self::SERVICE_VALIDATION => 'Validazione',
            self::SERVICE_ANALYSIS => 'Analisi',
            default => ucfirst($this->service_type),
        };
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->software_cost == 0) {
            return 0;
        }

        return (($this->charged_credits - $this->software_cost) / $this->software_cost) * 100;
    }

    public function getFormattedProfitMarginAttribute(): string
    {
        return number_format($this->profit_margin, 2, ',', '.') . '%';
    }

    public function getResponseTimeAttribute(): ?string
    {
        if (!$this->response_time_ms) {
            return null;
        }

        if ($this->response_time_ms < 1000) {
            return $this->response_time_ms . 'ms';
        }

        return number_format($this->response_time_ms / 1000, 2, ',', '.') . 's';
    }

    // Methods
    public function markAsSuccess(): void
    {
        $this->status = self::STATUS_SUCCESS;
        $this->save();
    }

    public function markAsFailed(string $errorMessage = null): void
    {
        $this->status = self::STATUS_FAILED;
        if ($errorMessage) {
            $this->error_message = $errorMessage;
        }
        $this->save();
    }

    public function markAsTimeout(): void
    {
        $this->status = self::STATUS_TIMEOUT;
        $this->save();
    }

    public function hasError(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_TIMEOUT]);
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public static function getServiceTypes(): array
    {
        return [
            self::SERVICE_OCR => 'OCR',
            self::SERVICE_SIGNATURE => 'Firma Digitale',
            self::SERVICE_TRANSLATION => 'Traduzione',
            self::SERVICE_VALIDATION => 'Validazione',
            self::SERVICE_ANALYSIS => 'Analisi',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SUCCESS => 'Successo',
            self::STATUS_FAILED => 'Fallito',
            self::STATUS_PENDING => 'In attesa',
            self::STATUS_TIMEOUT => 'Timeout',
        ];
    }
}
