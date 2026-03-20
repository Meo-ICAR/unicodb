<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentStatus extends Model
{
    protected $table = 'document_status';

    protected $fillable = [
        'name',
        'status',
        'is_ok',
        'is_rejected',
        'description',
    ];

    protected $casts = [
        'is_ok' => 'boolean',
        'is_rejected' => 'boolean',
    ];

    // Stati possibili
    const STATUSES = [
        'ASSENTE' => 'ASSENTE',
        'DA_VERIFICARE' => 'DA VERIFICARE',
        'IN_VERIFICA' => 'IN VERIFICA',
        'OK' => 'OK',
        'DIFFORME' => 'DIFFORME',
        'RICHIESTA_INFO' => 'RICHIESTA INFO',
        'ERRATO' => 'ERRATO',
        'ANNULLATO' => 'ANNULLATO',
    ];

    // Stati positivi
    const POSITIVE_STATUSES = ['OK'];

    // Stati negativi/rifiutati
    const REJECTED_STATUSES = ['DIFFORME', 'ERRATO', 'ANNULLATO'];

    // Stati in attesa
    const PENDING_STATUSES = ['DA VERIFICARE', 'IN VERIFICA', 'RICHIESTA INFO'];

    // Stati mancanti
    const MISSING_STATUSES = ['ASSENTE'];

    /**
     * Controlla se lo stato è positivo
     */
    public function isPositive(): bool
    {
        return in_array($this->status, self::POSITIVE_STATUSES);
    }

    /**
     * Controlla se lo stato è negativo/rifiutato
     */
    public function isRejectedStatus(): bool
    {
        return in_array($this->status, self::REJECTED_STATUSES);
    }

    /**
     * Controlla se lo stato è in attesa
     */
    public function isPending(): bool
    {
        return in_array($this->status, self::PENDING_STATUSES);
    }

    /**
     * Controlla se lo stato è mancante
     */
    public function isMissing(): bool
    {
        return in_array($this->status, self::MISSING_STATUSES);
    }

    /**
     * Ottiene la classe CSS per lo stato
     */
    public function getStatusClass(): string
    {
        return match ($this->status) {
            'OK' => 'success',
            'DIFFORME', 'ERRATO', 'ANNULLATO' => 'danger',
            'DA VERIFICARE', 'IN VERIFICA', 'RICHIESTA INFO' => 'warning',
            'ASSENTE' => 'secondary',
            default => 'primary',
        };
    }

    /**
     * Ottiene l'icona per lo stato
     */
    public function getStatusIcon(): string
    {
        return match ($this->status) {
            'OK' => 'heroicon-o-check-circle',
            'DIFFORME' => 'heroicon-o-x-circle',
            'ERRATO' => 'heroicon-o-exclamation-triangle',
            'ANNULLATO' => 'heroicon-o-x-mark',
            'DA VERIFICARE' => 'heroicon-o-clock',
            'IN VERIFICA' => 'heroicon-o-eye',
            'RICHIESTA INFO' => 'heroicon-o-information-circle',
            'ASSENTE' => 'heroicon-o-minus-circle',
            default => 'heroicon-o-question-mark-circle',
        };
    }
}
