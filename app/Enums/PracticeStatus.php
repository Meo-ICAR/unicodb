<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PracticeStatus: string implements HasLabel, HasColor
{
    case ISTRUTTORIA = 'working';
    case RESPINTA = 'rejected';
    case EROGATA = 'perfected';
    case RINNOVABILE = 'renewable';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ISTRUTTORIA => 'In Istruttoria',
            self::RESPINTA => 'Deliberata',
            self::EROGATA => 'Erogata',
            self::RINNOVABILE => 'Respinta',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ISTRUTTORIA => 'warning',
            self::RINNOVABILE => 'info',
            self::EROGATA => 'success',
            self::RESPINTA => 'danger',
        };
    }
}
