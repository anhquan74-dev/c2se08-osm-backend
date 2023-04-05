<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleDetails extends Model
{
    use HasFactory;
    // Relationship
    // n - n with User Table 
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_detail_users');
    }
    protected $fillable = [
        'role_name',
    ];
}