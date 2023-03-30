<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;
    // Relationship
    // Belong to User Table
    public function userCustomer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function userProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
    protected $fillable = [
        'customer_id',
        'provider_id',
        'content',
    ];
}
