<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Review extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'booking_id',
        'doctor_id',
        'patient_id',
        'rating',
        'comment',
    ];

     public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
