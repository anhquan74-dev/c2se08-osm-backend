<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasFactory;
    // Relationship
    // Belong to User Table
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
    protected $fillable = [
        'provider_id',
        'image',
    ];
}