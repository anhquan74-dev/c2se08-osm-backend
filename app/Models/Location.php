<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;
    // Relationship
    // Belong to User Table
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    //
    public function appointment(): HasMany
    {
        return $this->hasMany(Appointment::class, 'location_id');
    }
    protected $fillable = [
        'user_id',
        'address',
        'province_id',
        'province_name',
        'district_id',
        'district_name',
        'ward_id',
        'ward_name',
        'coords_latitude',
        'coords_longitude',
        'is_primary',
        'type'
    ];
}
