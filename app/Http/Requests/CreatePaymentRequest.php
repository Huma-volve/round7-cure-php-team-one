<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'exists:bookings,id'],
            'gateway' => ['required', Rule::in(['stripe', 'paypal', 'cash'])],
            'currency' => ['required', 'string', 'size:3'],
            'amount' => ['required', 'numeric', 'min:0.5'],
            'description' => ['nullable', 'string', 'max:255'],
            'return_url' => ['nullable', 'url', 'required_if:gateway,paypal'],
            'cancel_url' => ['nullable', 'url', 'required_if:gateway,paypal'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => __('validation.booking_id.required'),
            'booking_id.exists' => __('validation.booking_id.exists'),
            'gateway.required' => __('validation.gateway.required'),
            'gateway.in' => __('validation.gateway.in'),
            'currency.required' => __('validation.currency.required'),
            'currency.size' => __('validation.currency.size'),
            'amount.required' => __('validation.amount.required'),
            'amount.numeric' => __('validation.amount.numeric'),
            'amount.min' => __('validation.amount.min'),
            'description.max' => __('validation.description.max'),
            'return_url.url' => __('validation.return_url.url'),
            'return_url.required_if' => __('validation.return_url.required_if'),
            'cancel_url.url' => __('validation.cancel_url.url'),
        ];
    }
}
