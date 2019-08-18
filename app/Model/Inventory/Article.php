<?php

namespace App\Model\Inventory;

use App\Model;
use App\Organization;
use App\Presenters\Article\UrlPresenter;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_articles';

    protected $guarded = [];

    protected $hidden = ['url'];

    protected $appends = ['url'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function getUrlAttribute()
    {
        return new UrlPresenter($this->organization, $this);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'invoice_bill_product');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
