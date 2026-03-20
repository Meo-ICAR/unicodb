<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiAgentis extends Model
{
    use HasFactory;

    protected $table = 'rui_agentis';

    protected $fillable = [
        'numero_iscrizione_d',
        'numero_iscrizione_a',
        'data_conferimento',
        'codice_compagnia',
        'ragione_sociale',
    ];

    protected $casts = [
        'data_conferimento' => 'datetime',
    ];

    public function ruiD()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_d', 'numero_iscrizione_rui');
    }

    public function ruiA()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_a', 'numero_iscrizione_rui');
    }
}
