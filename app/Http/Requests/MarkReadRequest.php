<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class MarkReadRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'chat_id' => ['required','exists:chats,id'],
            'message_ids' => ['required','array'],
            'message_ids.*' => ['integer','exists:messages,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $chat = Chat::find($this->input('chat_id'));
            if (! $chat) {
                $validator->errors()->add('chat_id', 'Chat not found.');
                return;
            }

            if (! in_array($this->user()->id, [$chat->user_one_id, $chat->user_two_id])) {
                $validator->errors()->add('authorization', 'You are not part of this chat.');
            }
        });
    }
}
