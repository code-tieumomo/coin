<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'status',
        'icon',
        'url',
        'action_text',
        'action_url',
    ];
}
