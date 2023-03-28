<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;
    // Relationship
    // 1 - n with Package Table
    public function package(): HasMany
    {
        return $this->hasMany(Package::class, 'service_id');
    }
    // Belong to User Table
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
    // Belong to Category Table
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    protected $fillable = [
        'category_id',
        'provider_id',
        'avg_price',
        'max_price',
        'min_price',
        'is_negotiable',
        'total_rate',
        'total_star',
        'avg_star',
        'number_of_packages',
        'valid_flag',
    ];
}
