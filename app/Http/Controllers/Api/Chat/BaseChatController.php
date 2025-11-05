<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;

class BaseChatController extends Controller
{
    protected function getUserChatsQuery($user)
    {
        // return query builder for chats where user is participant
        return Chat::where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)
              ->orWhere('user_two_id', $user->id);
        });
    }

    /**
     * Get or create a 1:1 chat between two users.
     * We standardize order to keep unique constraint.
     */
    protected function getOrCreateChatBetween(int $a, int $b)
    {
        if ($a === $b) {
            return null;
        }
        $userOne = min($a, $b);
        $userTwo = max($a, $b);

        $chat = Chat::where('user_one_id', $userOne)
                    ->where('user_two_id', $userTwo)
                    ->first();

        if (! $chat) {
            $chat = Chat::create([
                'user_one_id' => $userOne,
                'user_two_id' => $userTwo,
            ]);
        }

        return $chat;
    }
}
