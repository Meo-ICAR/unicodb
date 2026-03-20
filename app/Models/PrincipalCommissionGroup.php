<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PrincipalCommissionGroup extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'principal_id',
        'invoice_at',
        'total_commission_amount',
        'total_invoice_amount',
        'commission_percentage',
        'sales_invoice_id',
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

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function scopeMatched($query)
    {
        return $query->where('is_matched', true);
    }

    public function scopeUnmatched($query)
    {
        return $query->where('is_matched', false);
    }

    public function scopeForPrincipal($query, $principalId)
    {
        return $query->where('principal_id', $principalId);
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
