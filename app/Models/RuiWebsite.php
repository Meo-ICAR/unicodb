<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuiWebsite extends Model
{
    protected $fillable = [
        'numero_iscrizione_rui',
        'web_url',
    ];

    protected $casts = [
        'numero_iscrizione_rui' => 'string',
        'web_url' => 'string',
    ];

    public function rui()
    {
        return $this->belongsTo(Rui::class, 'numero_iscrizione_rui', 'numero_iscrizione_rui');
    }
}
