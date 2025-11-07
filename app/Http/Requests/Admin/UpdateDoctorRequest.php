<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $doctorId = $this->route('id');
        $doctor = \App\Models\Doctor::findOrFail($doctorId);
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $doctor->user_id,
            'mobile' => 'required|string|max:20',
            'specialty_id' => 'required|exists:specialties,id',
            'license_number' => 'required|string|max:100|unique:doctors,license_number,' . $doctorId,
            'clinic_address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'session_price' => 'required|numeric|min:0',
            'availability_json' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'mobile.required' => 'رقم الهاتف مطلوب',
            'specialty_id.required' => 'التخصص مطلوب',
            'license_number.required' => 'رقم الترخيص مطلوب',
            'license_number.unique' => 'رقم الترخيص مستخدم بالفعل',
            'session_price.required' => 'سعر الجلسة مطلوب',
        ];
    }
}
