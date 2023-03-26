<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
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
