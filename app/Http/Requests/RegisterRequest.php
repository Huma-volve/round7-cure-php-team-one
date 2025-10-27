<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults()],
            'mobile' => ['required','min:11','numeric','unique:users,mobile'],
            'profile_photo' => ['nullable','image','mimes:jpg,png,jpeg,gif,svg','dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000','max:2048'],
            'birthdate' => ['required','date','date_format:Y-m-d'],
            'gender' => ['required','in:male,female,other'],
            'location_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'location_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'medical_notes'=>['nullable','string'],
        ];
    }
}
