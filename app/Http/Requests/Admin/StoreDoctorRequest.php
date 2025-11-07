<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'mobile' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'specialty_id' => 'required|exists:specialties,id',
            'license_number' => 'required|string|max:100|unique:doctors,license_number',
            'clinic_address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'session_price' => 'required|numeric|min:0',
            'availability_json' => 'nullable|array',
            'consultation' => 'nullable|in:home,clinic,both',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'mobile.required' => 'رقم الهاتف مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'specialty_id.required' => 'التخصص مطلوب',
            'license_number.required' => 'رقم الترخيص مطلوب',
            'license_number.unique' => 'رقم الترخيص مستخدم بالفعل',
            'session_price.required' => 'سعر الجلسة مطلوب',
        ];
    }
}
