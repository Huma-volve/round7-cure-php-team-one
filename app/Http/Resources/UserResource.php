<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'birthdate' => optional($this->birthdate)->format('Y-m-d'),
            'gender' => $this->gender ?? null,
            'profile_photo' => $this->profile_photo ? asset($this->profile_photo) : null,
            'role' => $this->getRoleNames()->first(),
            'doctor' => $this->when($this->hasRole('doctor'), new DoctorResource($this->doctor)),
            'patient' => $this->when($this->hasRole('patient'), [
                'birthdate' => optional(optional($this->patient)->birthdate)->format('Y-m-d'),
                'medical_notes' => optional($this->patient)->medical_notes,
            ]),
        ];
    }
}