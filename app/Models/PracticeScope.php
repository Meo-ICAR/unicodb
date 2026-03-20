<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;  // <--- Import corretto
use Illuminate\Database\Eloquent\Relations\HasMany;  // <--- Per la relazione scopes
use Illuminate\Database\Eloquent\Model;

class PracticeScope extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'oam_code',
        'is_oneclient',
        'tipo_prodotto'
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(ProcessTask::class)->orderBy('sort_order');
    }

    public function oamScope()
    {
        return $this->belongsTo(OamScope::class, 'oam_code');
    }

    public function practiceOamscope(): HasMany
    {
        return $this->hasMany(
            Practice::class,
            'tipo_prodotto',  // Chiave esterna su practice_scopes
            'tipo_prodotto'  // Chiave proprietaria su practices
        );
    }

    public function oamName()
    {
        return $this->oamScope ? ($this->oamScope->code . ' ' . $this->oamScope->name) : '--';
    }
}
