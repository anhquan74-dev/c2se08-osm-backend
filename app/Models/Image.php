<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
	use HasFactory;
	// Relationship
	// Belong to User Appointment Table
	public function appointment(): BelongsTo
	{
		return $this->belongsTo(Appointment::class, 'appointment_id');
	}
	protected $fillable = [
		'asset_type',
		'delivery_type',
		'public_id',
		'file_name',
		'mime',
		'parent_type',
		'parent_id',
	];
}
