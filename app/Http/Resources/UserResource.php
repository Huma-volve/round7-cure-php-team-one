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
            'birthdate' => $this->birthdate,
            'profile_photo' => $this->profile_photo ? asset($this->profile_photo) : null,
            'patient' => [
                'birthdate' => optional($this->patient)->birthdate,
                'gender' => optional($this->patient)->gender,
                'medical_notes' => optional($this->patient)->medical_notes,
            ],
        ];
    
    }
}
