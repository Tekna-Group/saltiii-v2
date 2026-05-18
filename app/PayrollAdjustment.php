<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayrollAdjustment extends Model
{
    protected $table = 'payroll_adjustments';

    protected $fillable = [
        'payment_posting_id',
        'type',
        'description',
        'amount',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function paymentPosting()
    {
        return $this->belongsTo(PaymentPosting::class);
    }
}
