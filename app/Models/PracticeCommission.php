<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PracticeCommission extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'practice_id',
        'agent_id',
        'principal_id',
        'proforma_id',
        'practice_commission_status_id',
        'CRM_code',
        'inserted_at',
        'is_enasarco',
        'is_insurance',
        'is_payment',
        'is_recurrent',
        'is_prize',
        'is_client',
        'is_coordination',
        'tipo',
        'name',
        'amount',
        'description',
        'status_payment',
        'status_at',
        'perfected_at',
        'cancellation_at',
        'invoice_number',
        'invoice_at',
        'paided_at',
        'is_storno',
        'storned_at',
        'storno_amount',
        'alternative_number_invoice',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'storno_amount' => 'decimal:2',
        'inserted_at' => 'date',
        'status_at' => 'date',
        'storned_at' => 'date',
        'created_at' => 'datetime',
        'perfected_at' => 'date',
        'updated_at' => 'datetime',
        'cancellation_at' => 'date',
        'invoice_at' => 'date',
        'paided_at' => 'date',
        'is_enasarco' => 'boolean',
        'is_insurance' => 'boolean',
        'is_payment' => 'boolean',
        'is_recurrent' => 'boolean',
        'is_prize' => 'boolean',
        'is_client' => 'boolean',
        'is_coordination' => 'boolean',
        'is_storno' => 'boolean',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function proforma()
    {
        return $this->belongsTo(Proforma::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    public function practiceCommissionStatus()
    {
        return $this->belongsTo(PracticeCommissionStatus::class);
    }

    public function isPerfectedStatus()
    {
        return $this->practiceCommissionStatus?->is_perfectioned ?? false;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
