<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;
    // Relationship
    // 1 - 1 with Feedback table
    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'appointment_id');
    }
    // Belong to Package Table
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    protected $fillable = [
        'service_id',
        'package_id',
        'provider_id',
        'customer_id',
        'full_name',
        'phone_number',
        'attach_photos',
        'note_for_provider',
        'location',
        'date',
        'price',
        'price_unit',
        'status',
        'offer_date',
        'complete_date',
        'cancel_date',
        'feedback_id',
    ];
}
