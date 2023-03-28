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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role_id',
        'birthday',
        'gender',
        'phone_number',
        'avatar',
        'banner_photos',
        'introduction',
        'favorite',
        'appointment_schedule',
        'total_rate',
        'total_star',
        'avg_star',
        'clicks',
        'views',
        'click_rate',
        'valid_flag',
    ];
    // Relationship 
    // 1 - n with Favorite table
    public function favorite1(): HasMany
    {
        return $this->hasMany(Favorite::class, 'customer_id');
    }
    public function favorite2(): HasMany
    {
        return $this->hasMany(Favorite::class, 'provider_id');
    }
    // 1 - n with location
    public function location(): HasMany
    {
        return $this->hasMany(Location::class, 'user_id');
    }
    // 1 - n with service
    public function service(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
