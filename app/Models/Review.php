<?php

namespace App\Models;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //
    protected $fillable = [
        'booking_id',
        'patient_id',
        'doctor_id',
        'rating',
        'comment'
    ];

   

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }


}
