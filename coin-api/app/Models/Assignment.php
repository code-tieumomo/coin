<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'is_public',
    ];

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function subnets()
    {
        return $this->belongsToMany(Subnet::class)->withPivot('weight', 'needed');
    }
}
