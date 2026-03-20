<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiMandati extends Model
{
    use HasFactory;

    protected $table = 'rui_mandati';

    protected $fillable = [
        'oss',
        'matricola',
        'codice_compagnia',
        'ragione_sociale',
    ];

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'matricola', 'numero_iscrizione_rui');
    }
}
