<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'opened_by',
        'type',
        'status',
        'resolution_notes',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}


