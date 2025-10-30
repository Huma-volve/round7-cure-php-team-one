<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PatientChatController extends BaseChatController
{
    public function index()
    {
        $user = Auth::user();

        // For patient, per requirement: show all doctors (so patient can start new chat)
        $doctors = User::role('doctor')->select('id','name','email')->get();

        // Additionally, could include existing chats list (optional)
        $existingChats = $this->getUserChatsQuery($user)
            ->with(['messages' => function ($q) {
                $q->latest()->limit(1);
            }, 'userOne:id,name,email','userTwo:id,name,email'])
            ->latest('last_message_at')
            ->get()
            ->map(function ($chat) use ($user) {
                $other = $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;
                return [
                    'chat_id' => $chat->id,
                    'other_user' => $other ? ['id'=>$other->id,'name'=>$other->name,'email'=>$other->email] : null,
                    'last_message' => $chat->last_message,
                    'last_message_at' => $chat->last_message_at,
                ];
            });

        return response()->json([
            'role' => 'patient',
            'doctors' => $doctors,
            'existing_chats' => $existingChats,
        ]);
    }
}
