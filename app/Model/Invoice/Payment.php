<?php

namespace App\Model\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'invoice_payments';
    protected $guarded = [];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
