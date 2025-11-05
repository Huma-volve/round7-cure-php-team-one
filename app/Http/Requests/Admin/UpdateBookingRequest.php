<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_time' => 'required|date|after:now',
            'status' => 'required|in:pending,confirmed,cancelled,rescheduled',
            'price' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'date_time.required' => 'تاريخ الحجز مطلوب',
            'date_time.after' => 'تاريخ الحجز يجب أن يكون في المستقبل',
            'status.required' => 'حالة الحجز مطلوبة',
            'status.in' => 'حالة الحجز غير صحيحة',
        ];
    }
}
