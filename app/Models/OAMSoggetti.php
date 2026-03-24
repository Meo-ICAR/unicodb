<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OAMSoggetti extends Model
{
    protected $fillable = [
        'denominazione_sociale',
        'autorizzato_ad_operare',
        'persona',
        'codice_fiscale',
        'domicilio_sede_legale',
        'elenco',
        'numero_iscrizione',
        'data_iscrizione',
        'stato',
        'data_stato',
        'causale_stato_note',
        'check_collaborazione',
        'dipendente_collaboratore_di',
        'numero_collaborazioni_attive',
    ];

    protected $casts = [
        'autorizzato_ad_operare' => 'boolean',
        'data_iscrizione' => 'date',
        'data_stato' => 'date',
        'numero_collaborazioni_attive' => 'integer',
    ];

    protected $table = 'o_a_m_soggetti';

    // Disable all validation and mass assignment protection
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            return true;  // Allow all mass assignments
        });
    }
}
