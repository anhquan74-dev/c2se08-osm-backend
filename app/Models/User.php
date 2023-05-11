<?php

namespace App\Models;

use App\Services\ImageService;
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

    //1 - n with Favorite table
    public function favoriteCustomer(): HasMany
    {
        return $this->hasMany(Favorite::class, 'customer_id');
    }
    public function favoriteProvider(): HasMany
    {
        return $this->hasMany(Favorite::class, 'provider_id');
    }
    //1 - n with Notify table
    public function notifyCustomer(): HasMany
    {
        return $this->hasMany(Notify::class, 'customer_id');
    }
    public function notifyProvider(): HasMany
    {
        return $this->hasMany(Notify::class, 'provider_id');
    }
    //1 - n with Message table
    public function messageCustomer(): HasMany
    {
        return $this->hasMany(Message::class, 'customer_id');
    }
    public function messageProvider(): HasMany
    {
        return $this->hasMany(Message::class, 'provider_id');
    }
    // 1 - n with location
    public function location(): HasMany
    {
        return $this->hasMany(Location::class, 'user_id');
    }
    // 1 - n with banner
    public function banner(): HasMany
    {
        return $this->hasMany(Image::class, 'parent_id','id') ->where('parent_type', '=', 'provider');
    }

    public function avatar() {
        return $this->hasOne(Image::class, 'parent_id','id')->where('parent_type','=', 'avatar');
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
        'is_valid',
    ];
    protected $appends = ['avatar _url', 'banner_url'];

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

    public function getBannerUrlAttribute(){
        $images = $this->banner;
        if(count($images)){
            $returnUrls = [];
            $service = new ImageService();
            foreach ($images as $image){
                $returnUrls[] = $service->getImageUrl($image->id);
            }
            return $returnUrls;
        }
        return [];
    }

    public function getAvatarUrlAttribute()
    {
        $image = $this->avatar;
        if ($image) {
            $service = new ImageService();
            return $service->getImageUrl($image->id);
        }
        return null;
    }
}
