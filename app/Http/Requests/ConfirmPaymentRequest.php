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
            'gateway' => ['required', Rule::in(['stripe', 'paypal'])],
            'payment_id' => ['required', 'string'],
        ];
    }
}
