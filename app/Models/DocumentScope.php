<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentScope extends Model
{
    protected $fillable = ['name', 'description', 'color_code'];

    public function documentTypes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'document_type_scope');
    }
}
