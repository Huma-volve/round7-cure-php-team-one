<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_time' => [
                'required',
                'date',
                'after:now',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'date_time.required' => 'يجب تحديد تاريخ ووقت الموعد الجديد',
            'date_time.after' => 'لا يمكن تحديد موعد في الماضي',
        ];
    }
}

