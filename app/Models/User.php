<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The name of the guard for the Spatie permissions.
     *
     * @var string
     */
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'birthdate',
        'profile_photo',
        'location_lat',
        'location_lng',
        'email_otp',
        'email_verified_at',
        'email_otp_expires_at',
        'email_otp_sent_at',
        'phone_otp' ,
        'phone_otp_expires_at',
    ];

public function favorites()
{
    return $this->belongsToMany(Doctor::class, 'favorites' ,'user_id', 'doctor_id')->withTimestamps();
}


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_otp_sent_at' => 'datetime',
            'email_otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date',
        ];
    }

    

    /**
     * Get the patient profile for this user
     */
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Get the doctor profile for this user
     */
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

}
