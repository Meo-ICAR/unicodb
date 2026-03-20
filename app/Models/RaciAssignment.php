<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaciAssignment extends Model
{
    protected $table = 'raci_assignments';

    /**
     * Indica se gli ID sono auto-incrementanti (visto che abbiamo id nella migration)
     */
    public $incrementing = true;

    protected $fillable = [
        'process_task_id',
        'business_function_id',
        'role',
    ];
}
