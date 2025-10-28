<?php

namespace App\Models;

use App\Constants\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    // Constants
    public const STATUS_PENDING = BookingStatus::PENDING;
    public const STATUS_CONFIRMED = BookingStatus::CONFIRMED;
    public const STATUS_CANCELLED = BookingStatus::CANCELLED;
    public const STATUS_RESCHEDULED = BookingStatus::RESCHEDULED;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'date_time',
        'payment_method',
        'status',
        'price',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * Get the doctor for this booking
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the patient for this booking
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the payment for this booking
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Check if booking is cancellable (not too close to appointment time)
     */
    public function isCancellable(): bool
    {
        // Allow cancellation if appointment is more than 24 hours away
        return $this->date_time->diffInHours(now()) > 24 && $this->status !== 'cancelled';
    }

    /**
     * Check if booking can be rescheduled
     */
    public function isReschedulable(): bool
    {
        return $this->status !== 'cancelled' && $this->date_time > now();
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date_time', '>=', now())
            ->where('status', 'confirmed');
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for cancelled bookings
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}

