<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // Relationship 
    // 1 - n with Favorite table
    // public function favorite1(): HasMany
    // {
    //     return $this->hasMany(Favorite::class, 'customer_id');
    // }
    // public function favorite2(): HasMany
    // {
    //     return $this->hasMany(Favorite::class, 'provider_id');
    // }
    // 1 - n with location
    public function location(): HasMany
    {
        return $this->hasMany(Location::class, 'user_id');
    }
    // 1 - n with banner
    public function banner(): HasMany
    {
        return $this->hasMany(Banner::class, 'provider_id');
    }
    // 1 - n with post
    public function post(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }
    // 1 - n with service
    public function service(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_id');
    }
    // 1 - n with appointment
    public function appointment(): HasMany
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }
    protected $fillable = [
        'email',
        'password',
        'full_name',
        'birthday',
        'gender',
        'phone_number',
        'avatar',
        'introduction',
        'is_favorite',
        'is_working',
        'total_rate',
        'total_star',
        'avg_star',
        'clicks',
        'views',
        'click_rate',
        'is_valid_flag',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}