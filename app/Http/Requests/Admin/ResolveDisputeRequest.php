<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResolveDisputeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:resolve,reject',
            'resolution_notes' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'الإجراء مطلوب',
            'action.in' => 'الإجراء غير صحيح',
            'resolution_notes.required' => 'ملاحظات الحل مطلوبة',
        ];
    }
}
