<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
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
        'valid_flag',
    ];
}
