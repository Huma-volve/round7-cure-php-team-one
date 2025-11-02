<?php

namespace App\Traits;

use Illuminate\Http\Request;

// trait HandlesRoleUpdates
// {
//     protected function handlePatientUpdate($user, Request $request)
//     {
//         $user->patient()->update([
//             'birthdate' => $request->birthdate ?? $user->birthdate,
//             'gender' => $request->gender ?? $user->gender,
//             'medical_notes' => $request->medical_notes ?? optional($user->patient)->medical_notes,
//         ]);
//     }

//     protected function handleDoctorUpdate($user, Request $request)
//     {
//         $user->doctor()->update([
//             'specialty' => $request->specialty ?? optional($user->doctor)->specialty,
//             'license_number' => $request->license_number ?? optional($user->doctor)->license_number,
//             'clinic_location' => $request->clinic_location ?? optional($user->doctor)->clinic_location,
//             'session_price' => $request->session_price ?? optional($user->doctor)->session_price,
//             'availability_json' =>$request->availability_json ?? optional($user->doctor)->availability_json,
//             'experience' =>$request->experience ?? optional($user->doctor)->experience,
//             'about_me' => $request->about_me ?? optional($user->doctor)->about_me,
//             'latitude' => $request->latitude ?? optional($user->doctor)->latitude,
//             'longitude' => $request->longitude ?? optional($user->doctor)->longitude,
//         ]);
//     }
// }



namespace App\Traits;

use Illuminate\Http\Request;

trait HandlesRoleUpdates
{
    protected function handlePatientUpdate($user, Request $request): void
    {
        $data = [];

        foreach (['birthdate', 'gender', 'medical_notes'] as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->$field;
            }
        }

        if (!empty($data)) {
            $user->patient()->update($data);
        }
    }

    protected function handleDoctorUpdate($user, Request $request): void
    {
        $data = [];

        foreach ([
            'specialty',
            'license_number',
            'clinic_location',
            'session_price',
            'availability_json',
            'experience',
            'about_me',
            'latitude',
            'longitude'
        ] as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->$field;
            }
        }

        if (!empty($data)) {
            $user->doctor()->update($data);
        }
    }
}

