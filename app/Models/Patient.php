<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Patient extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'birthdate',
        'gender',
        'medical_notes',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

     public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }


    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class)
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('date_time', 'desc');
    }


}
