<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;
    // Relationship
    // 1 - n with appointment
    public function appointment(): HasMany
    {
        return $this->hasMany(Appointment::class, 'package_id');
    }
    // Belong to Service Table
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    protected $fillable = [
        'service_id',
        'name',
        'description',
        'price',
        'total_rate',
        'total_star',
        'avg_star',
        'is_negotiable',
        'view_priority',
        'is_valid_flag',
    ];
}