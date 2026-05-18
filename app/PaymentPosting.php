<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TaskActivity;

class PaymentPosting extends Model
{
    protected $fillable = [
        'user_id',
        'reference_number',
        'wallet_address',
        'wallet_network',
        'payment_method',
        'date_from',
        'date_to',
        'total_hours',
        'total_amount',
        'status',
        'payment_approved_by',
        'approved_at',
        'notes',
    ];

    protected $dates = [
        'date_from',
        'date_to',
        'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->hasMany(TaskActivity::class, 'payment_reference', 'reference_number');
    }

    public function adjustments()
    {
        return $this->hasMany(PayrollAdjustment::class, 'payment_posting_id');
    }

    public function getNetAmountAttribute()
    {
        $adds    = $this->adjustments->where('type', 'add')->sum('amount');
        $deducts = $this->adjustments->where('type', 'deduct')->sum('amount');
        return $this->total_amount + $adds - $deducts;
    }
}
