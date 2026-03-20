<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_configuration_id',
        'api_loggable_type',
        'api_loggable_id',
        'endpoint',
        'method',
        'name',
        'request_payload',
        'response_payload',
        'status_code',
        'execution_time_ms',
        'error_message',
        'created_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'execution_time_ms' => 'integer',
        'status_code' => 'integer',
        'created_at' => 'datetime',
    ];

    public function apiConfiguration(): BelongsTo
    {
        return $this->belongsTo(ApiConfiguration::class);
    }

    public function apiLoggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isSuccessful(): bool
    {
        return $this->status_code >= 200 && $this->status_code < 300;
    }

    public function isClientError(): bool
    {
        return $this->status_code >= 400 && $this->status_code < 500;
    }

    public function isServerError(): bool
    {
        return $this->status_code >= 500;
    }

    public function getMethodColorAttribute(): string
    {
        return match ($this->method) {
            'GET' => 'success',
            'POST' => 'primary',
            'PUT' => 'warning',
            'DELETE' => 'danger',
            'PATCH' => 'info',
            default => 'gray',
        };
    }

    public function getExecutionTimeSecondsAttribute(): float
    {
        return $this->execution_time_ms / 1000;
    }

    public function scopeSuccessful($query)
    {
        return $query
            ->where('status_code', '>=', 200)
            ->where('status_code', '<', 300);
    }

    public function scopeFailed($query)
    {
        return $query->where(function ($query) {
            $query
                ->where('status_code', '<', 200)
                ->orWhere('status_code', '>=', 500);
        });
    }

    public function scopeByEndpoint($query, string $endpoint)
    {
        return $query->where('endpoint', $endpoint);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }
}
