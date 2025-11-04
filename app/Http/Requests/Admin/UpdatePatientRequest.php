<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patientId = $this->route('id');
        $patient = \App\Models\Patient::findOrFail($patientId);
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $patient->user_id,
            'mobile' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birthdate' => 'nullable|date',
            'medical_notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'mobile.required' => 'رقم الهاتف مطلوب',
            'gender.in' => 'الجنس غير صحيح',
            'birthdate.date' => 'تاريخ الميلاد غير صحيح',
        ];
    }
}
