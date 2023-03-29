<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;
    // Relationship
    // Belong to User Table
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    protected $fillable = [
        'title',
        'content',
        'description',
        'author_id',
        'date',
        'tags',
        'is_valid_flag',
    ];
}