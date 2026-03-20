<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'contactable_type',
        'contactable_id',
        'name',
        'phone',
        'email',
        'role_type',
        'description',
    ];

    protected $casts = [
        'company_id' => 'string',
    ];

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes utili
    public function scopeForClient($query)
    {
        return $query->where('contactable_type', Client::class);
    }

    public function scopeForPrincipal($query)
    {
        return $query->where('contactable_type', Principal::class);
    }

    public function scopeForAgent($query)
    {
        return $query->where('contactable_type', Agent::class);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role_type', $role);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('role_type', 'like', "%{$term}%");
        });
    }

    // Accessors
    public function getFullInfoAttribute(): string
    {
        $parts = [$this->name];

        if ($this->role_type) {
            $parts[] = "({$this->role_type})";
        }

        if ($this->email) {
            $parts[] = $this->email;
        }

        if ($this->phone) {
            $parts[] = $this->phone;
        }

        return implode(' - ', $parts);
    }

    public function getContactableTypeLabelAttribute(): string
    {
        return match ($this->contactable_type) {
            Client::class => 'Cliente',
            Principal::class => 'Banca/Principal',
            Agent::class => 'Agente',
            default => $this->contactable_type,
        };
    }
}
