<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class AgentCommissionGroup extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'agent_id',
        'invoice_at',
        'total_commission_amount',
        'total_invoice_amount',
        'commission_percentage',
        'purchase_invoice_id',
        'number_invoice',
        'is_matched',
        'notes',
    ];

    protected $casts = [
        'invoice_at' => 'date',
        'total_commission_amount' => 'decimal:2',
        'total_invoice_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'is_matched' => 'boolean',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function scopeMatched($query)
    {
        return $query->where('is_matched', true);
    }

    public function scopeUnmatched($query)
    {
        return $query->where('is_matched', false);
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('invoice_at', $date);
    }

    public function calculateCommissionPercentage()
    {
        if ($this->total_invoice_amount && $this->total_invoice_amount > 0) {
            return ($this->total_commission_amount / $this->total_invoice_amount) * 100;
        }
        return null;
    }

    public function updateCommissionPercentage()
    {
        $this->commission_percentage = $this->calculateCommissionPercentage();
        $this->save();
    }
}
