<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            "booking_id" => "required|integer|exists:bookings,id",
            // "user_id" => "required|integer|exists:users,id",
            "doctor_id" => "required|integer|exists:doctors,id",
            "rating" => "required|integer|min:1|max:5",
            "comment" => "nullable|string|max:1000",
        ];
    }

}
