<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'opened_by',
        'reason',
        'status',
        'resolution_notes',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}


