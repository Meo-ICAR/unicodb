<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Rappresenta un intermediario assicurativo iscritto al RUI (IVASS).
 *
 * @property int                      $id
 * @property string|null              $oss
 * @property bool                     $inoperativo
 * @property \Carbon\Carbon|null      $data_inizio_inoperativita
 * @property string                   $numero_iscrizione_rui
 * @property \Carbon\Carbon|null      $data_iscrizione
 * @property string|null              $cognome_nome
 * @property string|null              $stato
 * @property string|null              $comune_nascita
 * @property \Carbon\Carbon|null      $data_nascita
 * @property string|null              $ragione_sociale
 * @property string|null              $provincia_nascita
 * @property string|null              $titolo_individuale_sez_a
 * @property string|null              $attivita_esercitata_sez_a
 * @property string|null              $titolo_individuale_sez_b
 * @property string|null              $attivita_esercitata_sez_b
 * @property int|null                 $rui_section_id
 * @property \Carbon\Carbon|null      $created_at
 * @property \Carbon\Carbon|null      $updated_at
 *
 * @property-read \App\Models\RuiSection                                                          $ruiSection
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RuiWebsite>           $websites
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RuiCariche>           $carichePg
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RuiSedi>              $sedi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RuiCollaboratori>     $collaboratori
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RuiCollaboratori>     $collaboratoriILiv
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RuiCollaboratori>     $collaboratoriIILiv
 */
class Rui extends Model
{
    use HasFactory;

    protected $table = 'rui';

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
        'rui_section_id',
    ];

    protected $casts = [
        'inoperativo' => 'boolean',
        'data_inizio_inoperativita' => 'date',
        'data_iscrizione' => 'date',
        'data_nascita' => 'date',
    ];

    public function ruiSection()
    {
        return $this->belongsTo(RuiSection::class);
    }

    public function websites()
    {
        return $this->hasMany(RuiWebsite::class, 'numero_iscrizione_rui', 'numero_iscrizione_rui');
    }

    public function carichePg()
    {
        return $this->hasMany(RuiCariche::class, 'numero_iscrizione_rui_pg', 'numero_iscrizione_rui');
    }

    public function sedi()
    {
        return $this->hasMany(RuiSedi::class, 'numero_iscrizione_int', 'numero_iscrizione_rui');
    }

    public function collaboratori()
    {
        return $this->hasMany(RuiCollaboratori::class, 'num_iscr_intermediario', 'numero_iscrizione_rui');
    }

    public function collaboratoriILiv()
    {
        return $this->hasMany(RuiCollaboratori::class, 'num_iscr_collaboratori_i_liv', 'numero_iscrizione_rui');
    }

    public function collaboratoriIILiv()
    {
        return $this->hasMany(RuiCollaboratori::class, 'num_iscr_collaboratori_ii_liv', 'numero_iscrizione_rui');
    }

    /**
     * Calculate Italian codice fiscale from personal data.
     * Format: 3 letters from surname + name + birth date + birth place + check digit.
     * Returns an empty string if any required field (cognome_nome, data_nascita, comune_nascita) is missing.
     *
     * @return string
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
