<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'gateway' => ['required', Rule::in(['stripe', 'paypal', 'cash'])],
            'payment_id' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'gateway.required' => __('validation.gateway.required'),
            'gateway.in' => __('validation.gateway.in'),
            'payment_id.required' => __('validation.payment_id.required'),
            'payment_id.string' => __('validation.payment_id.string'),
        ];
    }
}
