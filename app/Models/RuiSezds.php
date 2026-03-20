<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiSezds extends Model
{
    use HasFactory;

    protected $table = 'rui_sezds';

    protected $fillable = [
        'numero_iscrizione_d',
        'ragione_sociale',
        'cognome_nome_responsabile',
    ];

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_d', 'numero_iscrizione_rui');
    }
}
