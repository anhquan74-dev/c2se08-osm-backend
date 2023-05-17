<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;
    // Relationship
    // 1 - 1 with Feedback Table
    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'appointment_id');
    }
    // 1 - n with AttachPhoto Table
    // public function attachPhoto(): HasMany
    // {
    //     return $this->hasMany(Image::class, 'parent_id')->where('parent_type', '=', 'appointment');
    // }
    public function attachPhoto(): HasOne
    {
        return $this->hasOne(Image::class, 'parent_id', 'id')->where('parent_type', '=', 'appointment');
    }

    // Belong to Package Table
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    // Belong to User Table
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    //
    public function service()
    {
        return $this->hasOneThrough(
            Service::class,
            Package::class,
            'id', # foreign key on intermediary -- packages
            'id', # foreign key on target -- services
            'package_id', # local key on this -- appointments (appointment - package_id)
            'service_id' # local key on intermediary -- packages (package - service_id)
        );
    }

    //
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    protected $fillable = [
        'package_id',
        'customer_id',
        'location_id',
        'note_for_provider',
        'date',
        'price',
        'price_unit',
        'status',
        'offer_date',
        'complete_date',
        'cancel_date',
        'job_status'
    ];
}
