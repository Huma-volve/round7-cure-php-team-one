<?php

namespace App\Http\Controllers\Api\Chat;

use Illuminate\Support\Facades\Auth;

class DoctorChatController extends BaseChatController
{
    public function index()
    {
        $user = Auth::user();

        // fetch chats with other participant data and last message
        $chats = $this->getUserChatsQuery($user)
            ->with([
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                },
                'userOne:id,name,email',
                'userTwo:id,name,email',
                'meta' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }
            ])
            ->latest('last_message_at')
            ->paginate(20);

        // map to include other_user
        $payload = $chats->through(function ($chat) use ($user) {
            $other = $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;
            $meta = $chat->meta->first() ?? null;
            return [
                'chat_id' => $chat->id,
                'other_user' => $other ? [
                    'id' => $other->id,
                    'name' => $other->name,
                    'email' => $other->email,
                ] : null,
                'last_message' => $chat->last_message,
                'last_message_at' => $chat->last_message_at,
                'meta' => $meta ? [
                    'favorite' => (bool) $meta->favorite,
                    'archived' => (bool) $meta->archived,
                    'muted' => (bool) $meta->muted,
                    'last_read_message_id' => $meta->last_read_message_id,
                ] : null,
            ];
        });

        return response()->json([
            'role' => 'doctor',
            'data' => $payload,
            'meta' => [
                'current_page' => $chats->currentPage(),
                'last_page' => $chats->lastPage(),
            ]
        ]);
    }
}
