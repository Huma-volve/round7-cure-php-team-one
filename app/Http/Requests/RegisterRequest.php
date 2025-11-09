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
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults(),'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'],
            'mobile' => ['required','min:11','numeric','unique:users,mobile','regex:/^01[0-2,5]{1}[0-9]{8}$/'],
            'profile_photo' => ['nullable','nullable','image','mimes:jpg,png,jpeg,gif,svg','max:2048'],
            'birthdate' => ['nullable','date','date_format:Y-m-d','before_or_equal:today'],
            'gender' => ['nullable','in:male,female'],
            'location_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'location_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'medical_notes'=>['nullable','string'],
        ];
    }
}
