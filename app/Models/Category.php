<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    // Relationship
    // 1 - n with service
    public function service(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }
    protected $fillable = [
        'name',
        'logo',
        'total_provider',
        'view_priority',
        'is_valid_flag',
    ];
}