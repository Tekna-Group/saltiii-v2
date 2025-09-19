<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //
    protected $fillable = [
        'invoice_number', 'service', 'description', 'amount', 
        'paid_status', 'payment_type', 'payment_description', 'paid_on','user_id'
    ];
}
