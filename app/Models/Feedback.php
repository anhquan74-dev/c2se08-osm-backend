<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $fillable = [
        'appointment_id',
        'customer_id',
        'package_id',
        'comment',
        'reply',
        'star',
        'reply_at',
    ];
}
