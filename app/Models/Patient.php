<?php

namespace App\Models;


use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Patient extends Model
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;
    protected $guard_name = 'api';
    protected $fillable = ['medical_notes', 'user_id','gender','birthdate'];
    

    protected $casts = [
        'birthdate' => 'date',
    ];
    public function user()
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
