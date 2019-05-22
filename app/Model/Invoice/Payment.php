<?php

namespace App\Model\Invoice;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'invoice_payments';
    protected $guarded = [];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
