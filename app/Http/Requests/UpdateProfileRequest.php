<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'name' => ['string', 'max:255'],
            'password' => ['nullable', Password::defaults(),'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'],
            'current_password' => ['required_with:password'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'birthdate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'gender' => ['nullable', 'in:male,female'],
            'location_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'location_lng' => ['nullable', 'numeric', 'between:-180,180'],


        ];


        if ($user && $user->hasRole('doctor')) {
            $rules = array_merge($rules, [
                'specialty' => ['nullable', 'string', 'max:255'],
                'license_number' => ['nullable', 'string', 'max:255'],
                'clinic_location' => ['nullable', 'string', 'max:255'],
                'session_price' => ['nullable', 'numeric', 'min:0'],
                'availability_json' => ['nullable', 'json'],
                'consultation_type' => ['nullable', 'array'],
                'consultation_type.*' => ['in:in_clinic,home_visit'],
                'experience' => ['nullable', 'integer', 'min:0'],
                'about_me' => ['nullable', 'string', 'max:5000'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            ]);
        }


        if ($user && $user->hasRole('patient')) {
            $rules = array_merge($rules, [
                'medical_notes' => ['nullable', 'string'],
            ]);
        }

        return $rules;
    }
}
