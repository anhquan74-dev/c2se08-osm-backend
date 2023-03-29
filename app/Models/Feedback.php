<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;
    // Relationship
    // Belong to Appointment 1 - 1 
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
    protected $fillable = [
        'appointment_id',
        'comment',
        'reply',
        'star',
        'reply_at',
    ];
}