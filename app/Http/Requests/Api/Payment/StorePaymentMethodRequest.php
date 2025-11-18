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
            'cardholder_name' => ['required', 'string', 'max:120'],
            'card_number' => ['required', 'digits_between:12,19'],
            'brand' => ['nullable', 'string', 'max:60'],
            'exp_month' => ['required', 'integer', 'between:1,12'],
            'exp_year' => ['required', 'integer', "between:$currentYear,$maxYear"],
            'cvv' => ['required', 'digits_between:3,4'],
            'gateway' => ['nullable', 'string', 'max:60'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
        ]);
    }
}

