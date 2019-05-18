<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     *  Restriction Key
     */
    const RESTRICTION_ORGANIZATION = 'RESTRICTION_ORGANIZATION';

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

    public function users()
    {
        return $this->belongsToMany(User::class, 'contributors');
    }

    public function addContributor(User $user)
    {
        $this->users()->attach($user->id);
    }

    public function removeContributor(User $user)
    {
        $this->users()->detach($user->id);
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
