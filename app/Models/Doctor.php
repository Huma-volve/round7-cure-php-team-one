<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
 {
    protected $fillable = [

    'user_id',
    'specialty_id',
    'license_number',
    'clinic_address',
    'latitude',
    'longitude',
    'session_price',
    'availability_json',
];
protected $appends = ['average_rating', 'reviews_count' , 'availability'];
protected $hidden = ['availability_json', 'created_at', 'updated_at'];


    public function user()
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
        return json_decode($this->availability_json, true);
    }

public function getAverageRatingAttribute()
{
    return round($this->reviews()->avg('rating') ?? 0, 2);
}

public function getReviewsCountAttribute()
{
    return $this->reviews()->count();
}


 }
