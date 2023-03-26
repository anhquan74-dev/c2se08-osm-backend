<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
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
        'primary_flag',
    ];
}
