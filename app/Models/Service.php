<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
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
