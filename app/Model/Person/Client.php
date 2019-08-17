<?php

namespace App\Model\Person;

use App\Model;
use App\Organization;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $table = 'person_clients';

    protected $guarded = [];

    protected $appends = ['all_due_amount'];

    public function getAllDueAmountAttribute()
    {
        return $this->bills->map->dueAmount()->sum();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = strtolower($value);
    }

    public function getLastNameAttribute($value)
    {
        return ucwords($value);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%')->orWhere('last_name', 'like', '%'.$search.'%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}