<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOnlineTimeLog extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'session_start',
        'session_end',
        'duration_minutes'
    ];

    protected $casts = [
        'date' => 'date',
        'session_start' => 'datetime',
        'session_end' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
