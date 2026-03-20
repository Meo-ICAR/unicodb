<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'name',
        'n_civico',
        'numero',
        'street',
        'city',
        'zip_code',
        'address_type_id',
        'addressable_type',
        'addressable_id',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function addressType()
    {
        return $this->belongsTo(AddressType::class);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->n_civico) {
            $parts[] = $this->n_civico;
        }

        if ($this->street) {
            $parts[] = $this->street;
        }

        if ($this->numero) {
            $parts[] = $this->numero;
        }

        if ($this->city) {
            $parts[] = $this->city;
        }

        if ($this->zip_code) {
            $parts[] = $this->zip_code;
        }

        return implode(', ', $parts);
    }

    public function getStreetWithNumberAttribute(): string
    {
        $street = $this->street ?? '';
        $numero = $this->numero ?? '';
        $n_civico = $this->n_civico ?? '';

        // Priorità: n_civico > numero
        $number = $n_civico ?: $numero;

        if ($street && $number) {
            return $street . ' ' . $number;
        }

        return $street . $number;
    }

    public function getFullStreetAttribute(): string
    {
        $parts = [];

        if ($this->address) {
            $parts[] = $this->address;
        }

        if ($this->n_civico) {
            $parts[] = $this->n_civico;
        }

        if ($this->street) {
            $parts[] = $this->street;
        }

        if ($this->numero) {
            $parts[] = $this->numero;
        }

        return implode(' ', $parts);
    }
}
