<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
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
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id|different:sender_id',
            'message' => 'nullable|string|max:5000',
            'file_url' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mp3,wav|max:10240', // 10MB max
            'is_read' => 'boolean',
            'archived' => 'boolean',
            'favorite' => 'boolean',
        ];
        
    }
        public function messages(): array
    {
        return [
            'sender_id.required' => 'The sender ID is required.',
            'receiver_id.required' => 'The receiver ID is required.',
            'receiver_id.different' => 'You cannot send a message to yourself.',
            'file_url.mimes' => 'Only image, video, or audio files are allowed.',
            'file_url.max' => 'File size must not exceed 10MB.',
        ];
    }

}
