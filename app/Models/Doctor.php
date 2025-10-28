<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty',
        'license_number',
        'clinic_address',
        'latitude',
        'longitude',
        'session_price',
        'availability_json',
    ];

    protected $casts = [
        'availability_json' => 'array',
        'session_price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user that owns the doctor
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all bookings for this doctor
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get confirmed/upcoming bookings
     */
    public function upcomingBookings(): HasMany
    {
        return $this->hasMany(Booking::class)
            ->where('status', 'confirmed')
            ->where('date_time', '>=', now())
            ->orderBy('date_time');
    }

}

