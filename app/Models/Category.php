<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;
    // Relationship
    // 1 - n with service
    public function service(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }
    // 1 - n with post
    public function post(): HasMany
    {
        return $this->hasMany(Post::class, 'category_id');
    }

    public function image():HasOne
    {
        return $this->hasOne(Image::class, 'parent_id', 'id')->where('parent_type','=','category');
    }

    protected $fillable = [
        'name',
        'logo',
        'total_provider',
        'view_priority',
        'is_valid',
    ];
    protected $appends = ['image_url'];

    protected $hidden = ['image'];
    public function getImageUrlAttribute(){
        $image = $this->image;
        if($image){
            $service = new ImageService();
            return $service->getImageUrl($image->id);
        }
        return null;
    }
}
