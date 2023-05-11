<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;
    // Relationship
    // Belong to Category Table
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function image(): HasOne
    {
        return $this->HasOne(Image::class, 'parent_id', 'id')->where('parent_type', '=', 'post');
    }
    protected $fillable = [
        'title',
        'content',
        'image',
        'category_id',
        'date',
        'tags',
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
