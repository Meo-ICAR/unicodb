<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'number',
        'order_number',
        'customer_number',
        'customer_name',
        'currency_code',
        'due_date',
        'amount',
        'amount_including_vat',
        'residual_amount',
        'ship_to_code',
        'ship_to_cap',
        'registration_date',
        'agent_code',
        'cdc_code',
        'dimensional_link_code',
        'location_code',
        'printed_copies',
        'payment_condition_code',
        'closed',
        'cancelled',
        'corrected',
        'email_sent',
        'email_sent_at',
        'bill_to_address',
        'bill_to_city',
        'bill_to_province',
        'ship_to_address',
        'ship_to_city',
        'payment_method_code',
        'customer_category',
        'exchange_rate',
        'vat_number',
        'bank_account',
        'document_residual_amount',
        'document_type',
        'credit_note_linked',
        'in_order',
        'supplier_number',
        'supplier_description',
        'purchase_invoice_origin',
        'sent_to_sdi',
        'invoiceable_type',
        'invoiceable_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'registration_date' => 'date',
        'amount' => 'decimal:2',
        'amount_including_vat' => 'decimal:2',
        'residual_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:2',
        'document_residual_amount' => 'decimal:2',
        'printed_copies' => 'integer',
        'closed' => 'boolean',
        'cancelled' => 'boolean',
        'corrected' => 'boolean',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
        'in_order' => 'boolean',
        'sent_to_sdi' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('cancelled', false);
    }

    public function scopeClosed($query)
    {
        return $query->where('closed', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('closed', false);
    }

    public function scopeForCustomer($query, $customerNumber)
    {
        return $query->where('customer_number', $customerNumber);
    }

    public function scopeForAgent($query, $agentCode)
    {
        return $query->where('agent_code', $agentCode);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('registration_date', [$startDate, $endDate]);
    }

    public function scopeByDocumentType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }

    public function getVatAmountAttribute()
    {
        return $this->amount_including_vat - $this->amount;
    }

    public function getOutstandingAmountAttribute()
    {
        return $this->amount_including_vat - $this->residual_amount;
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->residual_amount > 0;
    }

    public function getFormattedAmountAttribute()
    {
        return '€' . number_format($this->amount, 2, ',', '.');
    }

    public function getFormattedAmountIncludingVatAttribute()
    {
        return '€' . number_format($this->amount_including_vat, 2, ',', '.');
    }

    public function getFormattedResidualAmountAttribute()
    {
        return '€' . number_format($this->residual_amount, 2, ',', '.');
    }

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForInvoiceable($query, $model)
    {
        return $query
            ->where('invoiceable_type', get_class($model))
            ->where('invoiceable_id', $model->getKey());
    }

    public function scopeForInvoiceableType($query, string $type)
    {
        return $query->where('invoiceable_type', $type);
    }
}
