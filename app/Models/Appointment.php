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
    public function attachPhoto(): HasMany
    {
        return $this->hasMany(Image::class, 'parent_id')->where('parent_type', '=', 'appointment');
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
    protected $fillable = [
        'package_id',
        'customer_id',
        'note_for_provider',
        'location',
        'date',
        'price',
        'price_unit',
        'status',
        'offer_date',
        'complete_date',
        'cancel_date',
    ];

    protected $appends = ['image_url'];
    protected $hidden = ['attachPhoto'];
    public function getImageUrlAttribute(){
        $image = $this->attachPhoto;
        if($image){
            $service = new ImageService();
            return $service->getImageUrl($image->id);
        }
        return null;
    }
}
