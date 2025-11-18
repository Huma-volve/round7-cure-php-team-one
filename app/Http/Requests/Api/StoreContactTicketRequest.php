<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'source' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function validationData(): array
    {
        $data = parent::validationData();
        $data['priority'] = $data['priority'] ?? 'medium';
        return $data;
    }
}

