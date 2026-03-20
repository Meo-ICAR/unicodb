<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
