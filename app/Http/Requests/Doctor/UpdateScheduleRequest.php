<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('doctor') ?? false;
    }

    public function rules(): array
    {
        return [
            'schedule' => ['required', 'array'],
            'schedule.*.start' => ['nullable', 'date_format:H:i'],
            'schedule.*.end' => ['nullable', 'date_format:H:i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $schedule = $this->input('schedule', []);

        foreach ($schedule as $day => $slot) {
            if (empty($slot['start']) || empty($slot['end'])) {
                $schedule[$day]['start'] = null;
                $schedule[$day]['end'] = null;
            }
        }

        $this->merge(['schedule' => $schedule]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('schedule', []) as $day => $slot) {
                if (!empty($slot['start']) && !empty($slot['end']) && $slot['start'] >= $slot['end']) {
                    $validator->errors()->add("schedule.$day", __('وقت النهاية يجب أن يكون بعد وقت البداية'));
                }
            }
        });
    }
}

