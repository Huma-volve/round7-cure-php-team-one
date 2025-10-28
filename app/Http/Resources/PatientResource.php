<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'birthdate' => $this->birthdate?->format('Y-m-d'),
            'medical_notes' => $this->medical_notes,
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'name' => $this->user->name ?? null,
                    'email' => $this->user->email ?? null,
                    'mobile' => $this->user->mobile ?? null,
                    'profile_photo' => $this->user->profile_photo ?? null,
                ];
            }),
        ];
    }
}

