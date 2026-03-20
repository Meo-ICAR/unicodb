<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'supplier_invoice_number',
        'supplier_number',
        'supplier',
        'currency_code',
        'amount',
        'amount_including_vat',
        'pay_to_cap',
        'pay_to_country_code',
        'registration_date',
        'location_code',
        'printed_copies',
        'document_date',
        'payment_condition_code',
        'due_date',
        'payment_method_code',
        'residual_amount',
        'closed',
        'cancelled',
        'corrected',
        'pay_to_address',
        'pay_to_city',
        'supplier_category',
        'exchange_rate',
        'vat_number',
        'fiscal_code',
        'document_type',
        'company_id',
        'invoiceable_type',
        'invoiceable_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_including_vat' => 'decimal:2',
        'residual_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'registration_date' => 'date',
        'document_date' => 'date',
        'due_date' => 'date',
        'closed' => 'boolean',
        'cancelled' => 'boolean',
        'corrected' => 'boolean',
        'printed_copies' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeOpen($query)
    {
        return $query->where('closed', false);
    }

    public function scopePaid($query)
    {
        return $query->where('closed', true);
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
