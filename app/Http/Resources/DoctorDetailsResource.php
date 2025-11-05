<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'doctor' => new DoctorResource($this),
            'experience' => (int) $this->experience,
            'patient_count' => $this->bookings()->count(),
            'about_me' => $this->about_me,
            'session_price' => (float) $this->session_price,
            'availability' => $this->availability_json,
            "reviews" => $this->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => (float) $review->rating,
                    'comment' => $review->comment,
                    'user' => [
                        'id' => $review->patient?->user?->id,
                        'name' => $review->patient?->user?->name,
                        'profile_photo' => $review->patient?->user?->profile_photo,
                        'created_at' => $review->created_at->toDateTimeString(),
                    ],
                    'created_at' => $review->created_at->toDateTimeString(),
                ];
            }),

        ];

    }
}
