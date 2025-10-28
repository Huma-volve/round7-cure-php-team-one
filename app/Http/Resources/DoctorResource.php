<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'specialty' => $this->specialty,
            'license_number' => $this->license_number,
            'clinic_address' => $this->clinic_address,
            'location' => [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ],
            'session_price' => (float) $this->session_price,
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'name' => $this->user->name ?? null,
                    'email' => $this->user->email ?? null,
                    'mobile' => $this->user->mobile ?? null,
                    'profile_photo' => $this->user->profile_photo ?? null,
                ];
            }),
            'availability' => $this->availability_json,
        ];
    }
}

