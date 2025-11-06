<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             'id' => $this->id,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'medical_notes' => $this->medical_notes,

            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'mobile' => $this->user->mobile ?? null,
                'birthdate' => $this->user->birthdate ?? null,
            ],

            'bookings' => $this->bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'date_time' => $booking->date_time,
                    'payment_method' => $booking->payment_method,
                    'status' => $booking->status,
                    'price' => $booking->price,
                    'review' => $booking->review ? [
                        'rating' => $booking->review->rating,
                        'comment' => $booking->review->comment,
                    ]
                    : null,
                ];
            }),
        ];

    }
}
