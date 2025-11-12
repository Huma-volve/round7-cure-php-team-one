<?php

namespace App\Http\Requests\Api\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentYear = now()->year;
        $maxYear = $currentYear + 20;

        return [
            'provider' => ['required', 'in:card,apple_pay,paypal'],
            'brand' => ['nullable', 'string', 'max:100'],
            'last4' => ['nullable', 'digits:4'],
            'exp_month' => ['nullable', 'integer', 'between:1,12'],
            'exp_year' => ['nullable', 'integer', "between:$currentYear,$maxYear"],
            'gateway' => ['required', 'string', 'max:100'],
            'token' => ['required', 'string', 'max:255'],
            'is_default' => ['sometimes', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $provider = $this->input('provider');

            if ($provider === 'card') {
                if (!$this->filled('last4')) {
                    $validator->errors()->add('last4', __('validation.required', ['attribute' => 'last4']));
                }

                if (!$this->filled('exp_month') || !$this->filled('exp_year')) {
                    $validator->errors()->add('expiry', __('messages.payment_method.expiry_required'));
                }
            }
        });
    }
}

