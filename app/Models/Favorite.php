<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;
    // Relationship 
    // Belong to User Table
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
    protected $fillable = [
        'customer_id',
        'provider_id',
    ];
}
