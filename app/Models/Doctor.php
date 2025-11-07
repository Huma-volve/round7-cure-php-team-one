<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Doctor extends Model
{
    use HasFactory , Searchable;

    protected $fillable = [
        'user_id',
        'specialty_id',
        'license_number',
        'clinic_address',
        'latitude',
        'longitude',
        'session_price',
        'availability_json',
        'status',
    ];

   protected $appends = ['average_rating', 'reviews_count' ];
   protected $hidden = [ 'created_at', 'updated_at'];


    protected $casts = [
        'availability_json' => 'array',
        'session_price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'specialty' => $this->specialty->name,
            'clinic_address' => $this->clinic_address,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
{
    return $this->belongsTo(Specialty::class);
}


 public function favorites()
{
    return $this->belongsToMany(User::class, 'favorites');
}

    public function reviews()
{
    return $this->hasMany(Review::class);

}


    public function getAvailabilityAttribute()
    {
        $value = $this->availability_json;
        if (is_array($value)) {
            return $value;
        }

        if (is_null($value)) {
            return [];
        }
        
        return json_decode($value, true);
    }

public function getAverageRatingAttribute()
{
    return round($this->reviews()->avg('rating') ?? 0, 2);
}

public function getReviewsCountAttribute()
{
    return $this->reviews()->count();
}


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


       public function chats()
    {
        return $this->hasMany(Chat::class);
    }

}

