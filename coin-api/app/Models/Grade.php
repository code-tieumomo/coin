<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'user_id',
        'subnet_id',
        'grade',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subnet()
    {
        return $this->belongsTo(Subnet::class);
    }
}
