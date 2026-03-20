<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientMandate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'numero_mandato',
        'data_firma_mandato',
        'data_scadenza_mandato',
        'importo_richiesto_mandato',
        'scopo_finanziamento',
        'data_consegna_trasparenza',
        'stato',
    ];

    protected $casts = [
        'data_firma_mandato' => 'date',
        'data_scadenza_mandato' => 'date',
        'data_consegna_trasparenza' => 'date',
        'importo_richiesto_mandato' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function checklists()
    {
        // Un agente ha molte checklist (le sue copie assegnate)
        return $this->morphMany(Checklist::class, 'target');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }

    public function scopeAttivo($query)
    {
        return $query->where('stato', 'attivo');
    }

    public function scopeConclusoConSuccesso($query)
    {
        return $query->where('stato', 'concluso_con_successo');
    }

    public function scopeScaduto($query)
    {
        return $query->where('stato', 'scaduto');
    }

    public function scopeRevocato($query)
    {
        return $query->where('stato', 'revocato');
    }

    public function isAttivo(): bool
    {
        return $this->stato === 'attivo';
    }

    public function isConclusoConSuccesso(): bool
    {
        return $this->stato === 'concluso_con_successo';
    }

    public function isScaduto(): bool
    {
        return $this->stato === 'scaduto';
    }

    public function isRevocato(): bool
    {
        return $this->stato === 'revocato';
    }
}
