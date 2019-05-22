<?php

namespace App;

use Illuminate\Support\Str;
use App\Model\Invoice\{Bill, Product, Client};
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     *  Restriction Key
     */
    const RESTRICTION_ORGANIZATION = 'RESTRICTION_ORGANIZATION';
    const RESTRICTION_CONTRIBUTOR = 'RESTRICTION.CONTRIBUTOR';

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contributors()
    {
        return $this->belongsToMany(User::class, 'contributors');
    }

    public function addContributor(User $user)
    {
        $this->contributors()->attach($user->id);
    }

    public function removeContributor(User $user)
    {
        $this->contributors()->detach($user->id);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
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
