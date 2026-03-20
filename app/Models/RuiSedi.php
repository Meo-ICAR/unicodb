<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiSedi extends Model
{
    use HasFactory;

    protected $table = 'rui_sedi';

    protected $fillable = [
        'oss',
        'numero_iscrizione_int',
        'tipo_sede',
        'comune_sede',
        'provincia_sede',
        'cap_sede',
        'indirizzo_sede',
    ];

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_int', 'numero_iscrizione_rui');
    }
}
