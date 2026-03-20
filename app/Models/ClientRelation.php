<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientRelation extends Model
{
    protected $fillable = [
        'company_id',
        'client_id',
        'shares_percentage',
        'is_titolare',
        'client_type_id',
        'data_inizio_ruolo',
        'data_fine_ruolo',
    ];

    protected $casts = [
        'shares_percentage' => 'decimal:2',
        'is_titolare' => 'boolean',
        'data_inizio_ruolo' => 'date',
        'data_fine_ruolo' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }
}
