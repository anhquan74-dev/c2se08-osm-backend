<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;
    // Relationship
    // Belong to Category Table
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
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
}
