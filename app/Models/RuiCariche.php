<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiCariche extends Model
{
    use HasFactory;

    protected $table = 'rui_cariche';

    protected $fillable = [
        'oss',
        'numero_iscrizione_rui_pf',
        'numero_iscrizione_rui_pg',
        'qualifica_intermediario',
        'responsabile',
        'pf_name',
        'pg_name',
    ];

    public function ruiPf()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_rui_pf', 'numero_iscrizione_rui');
    }

    public function ruiPg()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_rui_pg', 'numero_iscrizione_rui');
    }
}
