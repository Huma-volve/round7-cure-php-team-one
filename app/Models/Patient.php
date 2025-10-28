<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'birthdate',
        'medical_notes',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    /**
     * Get the user that owns the patient
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all bookings for this patient
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get active bookings (not cancelled)
     */
    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class)
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('date_time', 'desc');
    }
}

