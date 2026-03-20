<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProformaStatus: string implements HasLabel, HasColor
{
    case INSERITO = 'INSERITO';
    case INVIATO = 'INVIATO';
    case ANNULLATO = 'ANNULLATO';
    case FATTURATO = 'FATTURATO';
    case PAGATO = 'PAGATO';
    case STORICO = 'STORICO';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INSERITO => 'Inserito',
            self::INVIATO => 'Inviato',
            self::ANNULLATO => 'Annullato',
            self::FATTURATO => 'Fatturato',
            self::PAGATO => 'Pagato',
            self::STORICO => 'Storico',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::INSERITO => 'gray',
            self::INVIATO => 'info',
            self::FATTURATO => 'success',
            self::PAGATO => 'success',
            self::ANNULLATO => 'danger',
            self::STORICO => 'warning',
        };
    }
}
