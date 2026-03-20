<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AmlQuestionnaire extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'qna_payload' => 'array',  // Converte automaticamente il JSON in array PHP
        'valid_until' => 'date',
    ];

    // --- RELAZIONI ---

    public function clientPractice(): BelongsTo
    {
        return $this->belongsTo(ClientPractice::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // --- SPATIE MEDIA LIBRARY CONFIGURATION ---

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('documento_firmato')
            ->useDisk('local')  // Usa un disco NON pubblico (es. storage/app/private) per il GDPR
            ->singleFile()  // Permette un solo PDF finale per questionario
            ->acceptsMimeTypes(['application/pdf']);
    }
}
