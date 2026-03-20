<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProcessTask extends Model
{
    //
    protected $fillable = ['practice_scope_id', 'name', 'slug', 'sort_order'];

    public function scope(): BelongsTo
    {
        return $this->belongsTo(PracticeScope::class, 'practice_scope_id');
    }

    public function businessFunctions(): BelongsToMany
    {
        return $this
            ->belongsToMany(BusinessFunction::class, 'raci_assignments')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Utility: Restituisce la matrice RACI strutturata per questo prodotto
     */
    public function getRaciMatrix(): \Illuminate\Support\Collection
    {
        return $this->tasks()->with('businessFunctions')->get()->map(function ($task) {
            return [
                'task_name' => $task->name,
                'responsibilities' => $task->businessFunctions->map(function ($func) {
                    return [
                        'function_name' => $func->name,
                        'function_code' => $func->code,
                        'role' => $func->pivot->role,  // R, A, C o I
                    ];
                })
            ];
        });
    }
}
