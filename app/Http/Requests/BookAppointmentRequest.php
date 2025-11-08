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
            'doctor_id.required' => __('validation.doctor_id.required'),
            'doctor_id.exists' => __('validation.doctor_id.exists'),
            'date_time.required' => __('validation.date_time.required'),
            'date_time.after' => __('validation.date_time.after'),
            'payment_method.required' => __('validation.payment_method.required'),
            'payment_method.in' => __('validation.payment_method.in'),
        ];
    }
}

