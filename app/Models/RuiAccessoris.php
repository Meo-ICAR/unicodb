<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiAccessoris extends Model
{
    use HasFactory;

    protected $table = 'rui_accessoris';

    protected $fillable = [
        'numero_iscrizione_e',
        'ragione_sociale',
        'cognome_nome',
        'sede_legale',
        'data_nascita',
        'luogo_nascita',
    ];

    protected $casts = [
        'data_nascita' => 'date',
    ];

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_e', 'numero_iscrizione_rui');
    }
}
