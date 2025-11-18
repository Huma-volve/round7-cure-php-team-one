<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;

class SendMessageRequest extends FormRequest
{
    public function authorize()
    {
        // السماح فقط للمستخدمين المسجلين
        return Auth::check();
    }

    public function rules()
    {
        return [
            'chat_id' => 'nullable|exists:chats,id',
            'receiver_id' => 'required_without:chat_id|exists:users,id',

            
            'body' => 'nullable|string',
            
            
   'type' => 'required|string|in:text,image,voice,video,pdf',
'attachment' => 'required_if:type,image,video,voice,pdf|file|mimes:pdf|max:51200',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->route('chat')) {
            $this->merge([
                'chat_id' => $this->route('chat') instanceof \App\Models\Chat
                    ? $this->route('chat')->id
                    : $this->route('chat'),
            ]);
        }
    }

    public function withValidator($validator)
    {
        // dd($validator()->all());
        $validator->after(function ($validator) {

            $user = $this->user();
            $chatId = $this->input('chat_id');

            // التحقق من وجود المحادثة فعلاً
            // $chat = Chat::find($chatId);
            \Log::info('Chat ID:', ['chat_id' => $chatId]);
            $chat = Chat::find($chatId);
            \Log::info('Chat:', ['chat' => $chat]);
            if (!$chat) {
                $validator->errors()->add('chat_id', 'Chat not found.');
                return;
            }

            // تأكد إن المستخدم طرف في المحادثة
            if (!in_array($user->id, [$chat->user_one_id, $chat->user_two_id])) {
                $validator->errors()->add('authorization', 'You are not part of this chat.');
                return;
            }

            // تأكد من عدم الإرسال السريع (spam)
            $lastMsg = $chat->messages()
                ->where('sender_id', $user->id)
                ->latest('created_at')
                ->first();

            if ($lastMsg && $lastMsg->created_at->diffInSeconds(now()) < 10) {
                $validator->errors()->add('throttle', 'Please wait before sending another message.');
                return;
            }

            // فحص المرفقات إن وجدت
            if ($this->hasFile('attachment')) {
                $file = $this->file('attachment');
                $mime = $file->getMimeType();
                $allowed = [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'image/gif',
                    'video/mp4',
                    'video/quicktime',
                    'audio/mpeg',
                    'audio/ogg',
                    'application/pdf',
                    'application/zip',
                    'application/octet-stream'
                ];

                if (!in_array($mime, $allowed)) {
                    $validator->errors()->add('attachment', 'Attachment type is not allowed.');
                }

                if ($file->getSize() > 50 * 1024 * 1024) {
                    $validator->errors()->add('attachment', 'Attachment size exceeds 50MB limit.');
                }
            }
        });
    }
}
