<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiCollaboratori extends Model
{
    use HasFactory;

    protected $table = 'rui_collaboratori';

    protected $fillable = [
        'oss',
        'livello',
        'num_iscr_intermediario',
        'num_iscr_collaboratori_i_liv',
        'num_iscr_collaboratori_ii_liv',
        'qualifica_rapporto',
        'intermediario',
        'collaboratore',
        'dipendente',
    ];

    public function intermediario()
    {
        return $this->belongsTo(Rui::class, 'num_iscr_intermediario', 'numero_iscrizione_rui');
    }

    public function collaboratoreILiv()
    {
        return $this->belongsTo(Rui::class, 'num_iscr_collaboratori_i_liv', 'numero_iscrizione_rui');
    }

    public function collaboratoreIILiv()
    {
        return $this->belongsTo(Rui::class, 'num_iscr_collaboratori_ii_liv', 'numero_iscrizione_rui');
    }
}
