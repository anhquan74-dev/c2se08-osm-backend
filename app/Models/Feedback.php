<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;
    // Relationship 
    // 1 - 1 with Appointment Table
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
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
