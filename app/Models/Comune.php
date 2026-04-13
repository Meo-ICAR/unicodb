<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Rappresenta un comune italiano dall'elenco ufficiale ISTAT.
 *
 * @property int         $id
 * @property string      $codice_regione
 * @property string      $codice_unita_territoriale
 * @property string      $codice_provincia_storico
 * @property string      $progressivo_comune
 * @property string      $codice_comune_alfanumerico
 * @property string      $denominazione
 * @property string      $denominazione_italiano
 * @property string      $denominazione_altra_lingua
 * @property string      $codice_ripartizione_geografica
 * @property string      $ripartizione_geografica
 * @property string      $denominazione_regione
 * @property string      $denominazione_unita_territoriale
 * @property string      $tipologia_unita_territoriale
 * @property bool        $capoluogo_provincia
 * @property string      $sigla_automobilistica
 * @property string      $codice_comune_numerico
 * @property string      $codice_comune_110_province
 * @property string      $codice_comune_107_province
 * @property string      $codice_comune_103_province
 * @property string      $codice_catastale
 * @property string      $codice_nuts1_2021
 * @property string      $codice_nuts2_2021
 * @property string      $codice_nuts3_2021
 * @property string      $codice_nuts1_2024
 * @property string      $codice_nuts2_2024
 * @property string      $codice_nuts3_2024
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read string $display_name  Denominazione preferita (italiano o default)
 */
class Comune extends Model
{
    protected $fillable = [
        'codice_regione',
        'codice_unita_territoriale',
        'codice_provincia_storico',
        'progressivo_comune',
        'codice_comune_alfanumerico',
        'denominazione',
        'denominazione_italiano',
        'denominazione_altra_lingua',
        'codice_ripartizione_geografica',
        'ripartizione_geografica',
        'denominazione_regione',
        'denominazione_unita_territoriale',
        'tipologia_unita_territoriale',
        'capoluogo_provincia',
        'sigla_automobilistica',
        'codice_comune_numerico',
        'codice_comune_110_province',
        'codice_comune_107_province',
        'codice_comune_103_province',
        'codice_catastale',
        'codice_nuts1_2021',
        'codice_nuts2_2021',
        'codice_nuts3_2021',
        'codice_nuts1_2024',
        'codice_nuts2_2024',
        'codice_nuts3_2024',
    ];

    protected $casts = [
        'capoluogo_provincia' => 'boolean',
    ];

    public function getDisplayNameAttribute()
    {
        return $this->denominazione_italiano ?? $this->denominazione;
    }
}
