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


        // 'id' => $this->id,
        // 'name' => $this->name,
        // 'email' => $this->email,
        // 'mobile' => $this->mobile,
        // 'birthdate' => $this->birthdate,
        // 'profile_photo' => $this->profile_photo ? asset($this->profile_photo) : null,
        // 'role' => $this->getRoleNames()->first(),
        // 'patient' => [
        //     'birthdate' => optional($this->patient)->birthdate,
        //     'gender' => optional($this->patient)->gender,
        //     'medical_notes' => optional($this->patient)->medical_notes,
        // ],
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'birthdate' => optional($this->birthdate)->format('Y-m-d'),
            'profile_photo' => $this->profile_photo ? asset($this->profile_photo) : null,
            'role' => $this->getRoleNames()->first(),


            'doctor' => $this->when($this->hasRole('doctor'), new DoctorResource($this->doctor)),


            'patient' => $this->when($this->hasRole('patient'), [
               'birthdate' => optional(optional($this->patient)->birthdate)->format('Y-m-d'),
                'gender' => optional($this->patient)->gender,
                'medical_notes' => optional($this->patient)->medical_notes,
            ]),
        ];
    }
}
