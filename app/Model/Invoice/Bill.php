<?php

namespace App\Model\Invoice;

use App\Organization;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'invoice_bills';
    protected $guarded = [];

    const TYPE_CASH = 'CONTADO';
    const TYPE_CREDIT = 'CREDITO';
    const TYPE_QUOTATION = 'COTIZACION';

    const STATUS_PAID = 'PAGADA';
    const STATUS_CANCEL = 'CANCELADA';
    const STATUS_CURRENT = 'EN PROCESO';
    const STATUS_EXPIRED = 'EXPIRADA';

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_bill_product');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
