<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuiRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_log_id',
        'practice_id',
        'client_id',
        'codice_univoco_aui',
        'tipo_registrazione',
        'data_registrazione',
        'importo_operazione',
        'profilo_rischio',
        'is_annullato',
        'motivo_annullamento',
        'company_id',
    ];

    protected $casts = [
        'data_registrazione' => 'date',
        'importo_operazione' => 'decimal:2',
        'is_annullato' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activityLog(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class);
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isAnnullato(): bool
    {
        return $this->is_annullato;
    }

    public function scopeAttivi($query)
    {
        return $query->where('is_annullato', false);
    }

    public function scopeAnnullati($query)
    {
        return $query->where('is_annullato', true);
    }

    public function scopeByTipoRegistrazione($query, string $tipo)
    {
        return $query->where('tipo_registrazione', $tipo);
    }

    public function scopeByProfiloRischio($query, string $profilo)
    {
        return $query->where('profilo_rischio', $profilo);
    }

    public function scopeByAnno($query, int $anno)
    {
        return $query->whereYear('data_registrazione', $anno);
    }

    public function scopeImportoRange($query, float $min, float $max)
    {
        return $query->whereBetween('importo_operazione', [$min, $max]);
    }

    public function getFormattedCodiceAuiAttribute(): string
    {
        return strtoupper($this->codice_univoco_aui);
    }

    public function getFormattedImportoAttribute(): string
    {
        return '€' . number_format($this->importo_operazione, 2, ',', '.');
    }

    public function getProfiloRischioLabelAttribute(): string
    {
        return match ($this->profilo_rischio) {
            'basso' => 'Basso',
            'medio' => 'Medio',
            'alto' => 'Alto',
            default => ucfirst($this->profilo_rischio),
        };
    }

    public function getTipoRegistrazioneLabelAttribute(): string
    {
        return match ($this->tipo_registrazione) {
            'instaurazione' => 'Instaurazione Rapporto',
            'esecuzione_operazione' => 'Esecuzione Operazione',
            'chiusura_rapporto' => 'Chiusura Rapporto',
            default => ucfirst($this->tipo_registrazione),
        };
    }
}
