<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuiIntermediari extends Model
{
    protected $table = 'rui_intermediaris';

    protected $fillable = [
        'oss',
        'inoperativo',
        'data_inizio_inoperativita',
        'numero_iscrizione_rui',
        'data_iscrizione',
        'cognome_nome',
        'stato',
        'comune_nascita',
        'data_nascita',
        'ragione_sociale',
        'provincia_nascita',
        'titolo_individuale_sez_a',
        'attivita_esercitata_sez_a',
        'titolo_individuale_sez_b',
        'attivita_esercitata_sez_b',
    ];

    protected $casts = [
        'inoperativo' => 'boolean',
        'data_inizio_inoperativita' => 'date',
        'data_iscrizione' => 'date',
        'data_nascita' => 'date',
    ];

    /**
     * Calculate Italian codice fiscale from personal data
     */
    public function calculateCodiceFiscale(): string
    {
        if (empty($this->cognome_nome) || empty($this->data_nascita) || empty($this->comune_nascita)) {
            return '';
        }

        // Split name into parts (surname, name)
        $nameParts = explode(' ', trim($this->cognome_nome));
        $surname = strtoupper($nameParts[0] ?? '');
        $name = strtoupper($nameParts[1] ?? '');

        // Get birth date parts
        $birthDate = \Carbon\Carbon::parse($this->data_nascita);
        $dayMonth = $birthDate->format('dmy');

        // Get birth place code (first 3 letters, last 3 letters)
        $birthPlace = strtoupper($this->comune_nascita);
        $birthPlaceCode = substr($birthPlace, 0, 3) . substr($birthPlace, -3);

        // Calculate check digit (simplified version)
        $checkDigit = $this->calculateCheckDigit($surname, $name, $dayMonth, $birthPlaceCode);

        return $surname . $name . $dayMonth . $birthPlaceCode . $checkDigit;
    }

    /**
     * Calculate check digit for codice fiscale
     */
    private function calculateCheckDigit(string $surname, string $name, string $dayMonth, string $birthPlaceCode): int
    {
        // Simplified check digit calculation
        $values = [];

        // Convert letters to numbers (A=0, B=1, C=2, etc.)
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $allChars = $surname . $name . $dayMonth . $birthPlaceCode;

        for ($i = 0; $i < strlen($allChars); $i++) {
            $char = strtoupper($allChars[$i]);
            $values[] = strpos($alphabet, $char) !== false ? strpos($alphabet, $char) : 0;
        }

        $sum = array_sum($values);
        return $sum % 26;
    }
}
