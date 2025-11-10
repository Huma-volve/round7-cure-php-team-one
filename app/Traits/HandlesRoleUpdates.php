<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait HandlesRoleUpdates
{
    protected function handlePatientUpdate($user, Request $request): void
    {
        $data = [];

        foreach (['birthdate', 'medical_notes'] as $field) {
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

        foreach (
            [
                'specialty',
                'license_number',
                'clinic_location',
                'session_price',
                'experience',
                'about_me',
                'latitude',
                'longitude',
                'consultation'
            ] as $field
        ) {
            if ($request->filled($field)) {
                $data[$field] = $request->$field;
            }
        }


        if ($request->has('availability_json')) {
            $oldAvailability = json_decode($user->availability_json ?? '{}', true);
            $dataToUpdate = json_decode($request->availability_json, true);

            if (isset($dataToUpdate['add'])) {
                foreach ($dataToUpdate['add'] as $day => $times) {
                    if (!isset($oldAvailability[$day])) {
                        $oldAvailability[$day] = [];
                    }

                    $oldAvailability[$day] = array_unique(array_merge(
                        (array) $oldAvailability[$day],
                        (array) $times
                    ));
                }
            }

            if (isset($dataToUpdate['remove'])) {
                foreach ($dataToUpdate['remove'] as $day => $times) {
                    if (isset($oldAvailability[$day])) {
                        $oldAvailability[$day] = array_values(array_diff(
                            (array) $oldAvailability[$day],
                            (array) $times
                        ));

                        if (empty($oldAvailability[$day])) {
                            unset($oldAvailability[$day]);
                        }
                    }
                }
            }


            $user->availability_json = json_encode($oldAvailability);
            $dataToUpdate = [
                'availability_json' => $user->availability_json,
            ];
            $user->doctor->update($dataToUpdate);
        }



        if (!empty($data)) {
            $user->doctor()->update($data);
        }
    }
}
