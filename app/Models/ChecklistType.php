<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_compliance',
        'color',
        'regulatory_body_id',
    ];

    protected $casts = [
        'is_compliance' => 'boolean',
    ];

    public function regulatoryBody()
    {
        return $this->belongsTo(RegulatoryBody::class);
    }
}
