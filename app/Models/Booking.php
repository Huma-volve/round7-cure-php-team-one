<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'date_time',
        'payment_method',
        'status',
        'price'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function review(){
        return $this->hasOne(Review::class , 'id');
    }
}
