<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Requests\Doctor\UpdateScheduleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduleController extends BaseDoctorController
{
    public function edit(): View
    {
        $doctor = $this->currentDoctor();
        $schedule = $doctor->availability_json ?? [];

        return view('doctor.schedule.edit', compact('doctor', 'schedule'));
    }

    public function update(UpdateScheduleRequest $request): RedirectResponse
    {
        $doctor = $this->currentDoctor();
        $scheduleInput = $request->validated()['schedule'] ?? [];

        $availability = [];
        foreach ($scheduleInput as $day => $slot) {
            $start = $slot['start'] ?? null;
            $end = $slot['end'] ?? null;

            if ($start && $end) {
                $availability[$day] = ["$start-$end"];
            }
        }

        $doctor->availability_json = $availability;
        $doctor->save();

        return redirect()
            ->route('doctor.schedule.edit')
            ->with('success', __('تم تحديث أوقات العمل بنجاح.'));
    }
}

