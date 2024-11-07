<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subnet extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
        'provider_embed_url',
        'miner_embed_url',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function assignments()
    {
        return $this->belongsToMany(Assignment::class)->withPivot('weight', 'needed');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
