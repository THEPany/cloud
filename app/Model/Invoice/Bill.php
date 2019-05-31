<?php

namespace App\Model\Invoice;

use Carbon\Carbon;
use App\Organization;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'invoice_bills';

    protected $guarded = [];

    protected $casts = [
        'expired_at' => 'date'
    ];

    protected $appends = [
        'total', 'due_amount'
    ];

    const TYPE_CASH = 'CONTADO';
    const TYPE_CREDIT = 'CREDITO';
    const ALL_BILL_TYPE = [self::TYPE_CASH, self::TYPE_CREDIT];

    const STATUS_PAID = 'PAGADA';
    const STATUS_CURRENT = 'EN PROCESO';
    const STATUS_EXPIRED = 'EXPIRADA';
    const STATUS_CANCEL = 'CANCELADA';
    const ALL_STATUS = [self::STATUS_PAID, self::STATUS_CURRENT, self::STATUS_EXPIRED, self::STATUS_CANCEL];

    public function getTotalAttribute()
    {
        return $this->total();
    }

    public function getDueAmountAttribute()
    {
        return $this->dueAmount();
    }

    public function setExpiredAtAttribute($value)
    {
        $this->attributes['expired_at'] = Carbon::createFromFormat('d-m-Y', $value)->toDateTimeString();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'invoice_bill_article')->withPivot([
            'cost',
            'quantity',
            'sub_total',
        ]);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subTotal()
    {
        return $this->articles->pluck('pivot')->sum->sub_total;
    }

    public function total()
    {
        return $this->subTotal() - $this->discount;
    }

    public function dueAmount()
    {
        return $this->total() - $this->payments->sum->paid_out;
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('id', 'like', '%'.$search.'%')
                ->orWhereHas('client', function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%');
                })->orWhere('total', 'like', '%'.$search.'%');
        })->when($filters['status'] ?? null, function ($query, $status) {
            if (in_array($status, self::ALL_STATUS)) {
                $query->where('status', $status);
            }
        })->when($filters['bill_type'] ?? null, function ($query, $bill_type) {
            if (in_array($bill_type, self::ALL_BILL_TYPE)) {
                $query->where('bill_type', $bill_type);
            }
        });
    }
}
