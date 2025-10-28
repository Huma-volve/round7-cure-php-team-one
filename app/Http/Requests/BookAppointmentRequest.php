<?php

namespace App\Http\Requests;

use App\Constants\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookAppointmentRequest extends FormRequest
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
            'doctor_id' => ['required', 'exists:doctors,id'],
            'date_time' => [
                'required',
                'date',
                'after:now',
            ],
            'payment_method' => ['required', Rule::in(PaymentMethod::all())],
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.required' => 'يجب اختيار طبيب',
            'doctor_id.exists' => 'الطبيب المحدد غير موجود',
            'date_time.required' => 'يجب تحديد تاريخ ووقت الموعد',
            'date_time.after' => 'لا يمكن حجز موعد في الماضي',
            'payment_method.required' => 'يجب اختيار طريقة الدفع',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
        ];
    }
}

