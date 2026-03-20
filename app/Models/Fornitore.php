<?php
// app/Models/Fornitore.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Fornitore extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fornitoris';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'codice',
        'coge',
        'name',
        'nome',
        'natoil',
        'indirizzo',
        'comune',
        'cap',
        'prov',
        'tel',
        'coordinatore',
        'piva',
        'cf',
        'nomecoge',
        'nomefattura',
        'email',
        'anticipo',
        'enasarco',
        'anticipo_residuo',
        'contributo',
        'contributo_description',
        'anticipo_description',
        'issubfornitore',
        'operatore',
        'iscollaboratore',
        'isdipendente',
        'regione',
        'citta',
        'company_id',
        'contributoperiodicita',
        'contributodalmese'
    ];

    protected $casts = [
        'natoil' => 'date',
        'anticipo' => 'decimal:2',
        'anticipo_residuo' => 'decimal:2',
        'contributo' => 'decimal:2',
        'issubfornitore' => 'boolean',
        'iscollaboratore' => 'boolean',
        'isdipendente' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'contributo_description' => 'Contributo spese',
        'anticipo_description' => 'Anticipo attuale',
        'issubfornitore' => false,
    ];
}
