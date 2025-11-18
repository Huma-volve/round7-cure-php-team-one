<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReplyTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:3'],
            'status' => ['nullable', 'in:open,pending,closed'],
        ];
    }
}

