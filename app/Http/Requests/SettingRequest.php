<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'app_name'    => 'nullable|string|max:255',
            'email'       => 'nullable|email',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|mimes:jpeg,jpg,png,bmp,gif,svg',
        ];

    }

    public function messages()
    {
        return [
            'logo.required' => 'The logo field is required.',
            'logo.image'    => 'The logo must be an image.',
            'logo.mimes'    => 'The logo must be a file of type: jpeg, jpg, png, bmp, gif, svg.',
        ];
    }
}
