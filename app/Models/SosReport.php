<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SosReport extends Model
{
    use HasFactory;
    use InteractsWithMedia;  // <--- Usa il trait di Spatie

    protected $fillable = [
        'aui_record_id',
        'client_mandate_id',
        'company_id',
        'codice_protocollo_interno',
        'stato',
        'grado_sospetto',
        'motivo_sospetto',
        'decisione_finali',
        'data_segnalazione_uif',
        'protocollo_uif',
        'responsabile_id',
    ];

    protected $casts = [
        'data_segnalazione_uif' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Relazione con AUI Record
     */
    public function auiRecord(): BelongsTo
    {
        return $this->belongsTo(AuiRecord::class);
    }

    /**
     * Relazione con il responsabile (User)
     */
    public function responsabile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsabile_id');
    }

    /**
     * Relazione con la Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function checklist()
    {
        // Un client privacy ha una sola checklist
        return $this->morphOne(Checklist::class, 'target');
    }

    /**
     * Scope per stato specifico
     */
    public function scopeByStato($query, $stato)
    {
        return $query->where('stato', $stato);
    }

    /**
     * Scope per grado sospetto
     */
    public function scopeByGradoSospetto($query, $grado)
    {
        return $query->where('grado_sospetto', $grado);
    }

    /**
     * Scope per reports da segnalare
     */
    public function scopeDaSegnalare($query)
    {
        return $query->where('stato', 'segnalata_uif');
    }

    /**
     * Scope per reports in istruttoria
     */
    public function scopeInIstruttoria($query)
    {
        return $query->where('stato', 'istruttoria');
    }

    /**
     * Verifica se è stato segnalato alla UIF
     */
    public function isSegnalataUif(): bool
    {
        return $this->stato === 'segnalata_uif';
    }

    /**
     * Verifica se è in istruttoria
     */
    public function isInIstruttoria(): bool
    {
        return $this->stato === 'istruttoria';
    }

    /**
     * Verifica se è archiviata
     */
    public function isArchiviata(): bool
    {
        return $this->stato === 'archiviata';
    }

    /**
     * Ottiene il colore del badge per lo stato
     */
    public function getStatoColorAttribute(): string
    {
        return match ($this->stato) {
            'istruttoria' => 'warning',
            'archiviata' => 'success',
            'segnalata_uif' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Ottiene il colore del badge per il grado sospetto
     */
    public function getGradoSospettoColorAttribute(): string
    {
        return match ($this->grado_sospetto) {
            'basso' => 'success',
            'medio' => 'warning',
            'alto' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Ottiene l'etichetta per lo stato
     */
    public function getStatoLabelAttribute(): string
    {
        return match ($this->stato) {
            'istruttoria' => 'Istruttoria',
            'archiviata' => 'Archiviata',
            'segnalata_uif' => 'Segnalata UIF',
            default => ucfirst($this->stato),
        };
    }

    /**
     * Ottiene l'etichetta per il grado sospetto
     */
    public function getGradoSospettoLabelAttribute(): string
    {
        return match ($this->grado_sospetto) {
            'basso' => 'Basso',
            'medio' => 'Medio',
            'alto' => 'Alto',
            default => ucfirst($this->grado_sospetto),
        };
    }
}
