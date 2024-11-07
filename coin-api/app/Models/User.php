<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles',
        'permissions',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['role', 'permission'];

    protected $guard_name = ['sanctum'];

    public function role(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getRoleNames()->first(),
        );
    }

    public function permission(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getAllPermissions()->pluck('name')->toArray(),
        );
    }

    public function subnets()
    {
        return $this->belongsToMany(Subnet::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function assignments()
    {
        return $this->belongsToMany(Assignment::class);
    }
}
