<?php

// namespace App\Http\Controllers\Api\Chat;

// use App\Models\Chat;
// use App\Models\ChatUserMeta;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class ChatController extends BaseChatController
// {
//     public function index(Request $request)
//     {
//         $user = auth()->user();
        
//         $chats = $this->getUserChatsQuery($user)
//             ->with(['userOne', 'userTwo', 'meta' => function($query) use ($user) {
//                 $query->where('user_id', $user->id);
//             }])
//             ->withCount(['messages as unread_count' => function($query) use ($user) {
//                 $query->where('receiver_id', $user->id)
//                       ->whereDoesntHave('reads', function($q) use ($user) {
//                           $q->where('user_id', $user->id);
//                       });
//             }])
//             ->orderBy('last_message_at', 'desc')
//             ->get();

//         // Filter based on request
//         if ($request->has('favorite')) {
//             $chats = $chats->filter(fn($chat) => $chat->meta->first()?->favorite);
//         }
        
//         if ($request->has('archived')) {
//             $chats = $chats->filter(fn($chat) => $chat->meta->first()?->archived);
//         }

//         return response()->json([
//             'status' => true,
//             'data' => $chats
//         ]);
//     }

//     public function getOrCreateChat($userId)
//     {
//         $user = auth()->user();
        
//         $chat = $this->getOrCreateChatBetween($user->id, $userId);
        
//         if (!$chat) {
//             return response()->json(['error' => 'Cannot create chat with yourself'], 400);
//         }

//         return response()->json([
//             'status' => true,
//             'data' => $chat->load(['userOne', 'userTwo'])
//         ]);
//     }

//     public function toggleFavorite($chatId)
//     {
//         $user = auth()->user();
        
//         $meta = ChatUserMeta::firstOrCreate([
//             'chat_id' => $chatId,
//             'user_id' => $user->id
//         ]);
        
//         $meta->update(['favorite' => !$meta->favorite]);
        
//         return response()->json([
//             'status' => true,
//             'favorited' => $meta->favorite
//         ]);
//     }

//     public function toggleArchive($chatId)
//     {
//         $user = auth()->user();
        
//         $meta = ChatUserMeta::firstOrCreate([
//             'chat_id' => $chatId,
//             'user_id' => $user->id
//         ]);
        
//         $meta->update(['archived' => !$meta->archived]);
        
//         return response()->json([
//             'status' => true,
//             'archived' => $meta->archived
//         ]);
//     }

//     public function getChatMessages($chatId)
//     {
//         $user = auth()->user();
        
//         $chat = Chat::where('id', $chatId)
//             ->where(function($query) use ($user) {
//                 $query->where('user_one_id', $user->id)
//                       ->orWhere('user_two_id', $user->id);
//             })
//             ->firstOrFail();

//         $messages = $chat->messages()
//             ->with(['sender', 'attachments', 'reads'])
//             ->orderBy('created_at', 'desc')
//             ->paginate(50);

//         return response()->json([
//             'status' => true,
//             'data' => $messages
//         ]);
//     }
// }







namespace App\Http\Controllers\Api\Chat;

use App\Models\Chat;
use App\Models\ChatUserMeta;
use App\Models\Message;
use App\Models\MessageRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends BaseChatController
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $chats = $this->getUserChatsQuery($user)
            ->with(['userOne', 'userTwo', 'meta' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->withCount(['messages as unread_count' => function($query) use ($user) {
                $query->where('receiver_id', $user->id)
                      ->whereDoesntHave('reads', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Filter based on request
        if ($request->has('favorite')) {
            $chats = $chats->filter(fn($chat) => $chat->meta->first()?->favorite);
        }
        
        if ($request->has('archived')) {
            $chats = $chats->filter(fn($chat) => $chat->meta->first()?->archived);
        }

        return response()->json([
            'status' => true,
            'data' => $chats
        ]);
    }

    public function getOrCreateChat($userId)
    {
        $user = auth()->user();
        
        $chat = $this->getOrCreateChatBetween($user->id, $userId);
        
        if (!$chat) {
            return response()->json(['error' => 'Cannot create chat with yourself'], 400);
        }

        return response()->json([
            'status' => true,
            'data' => $chat->load(['userOne', 'userTwo'])
        ]);
    }

    public function toggleFavorite($chatId)
    {
        $user = auth()->user();
        
        $meta = ChatUserMeta::firstOrCreate([
            'chat_id' => $chatId,
            'user_id' => $user->id
        ]);
        
        $meta->update(['favorite' => !$meta->favorite]);
        
        return response()->json([
            'status' => true,
            'favorited' => $meta->favorite
        ]);
    }

    public function toggleArchive($chatId)
    {
        $user = auth()->user();
        
        $meta = ChatUserMeta::firstOrCreate([
            'chat_id' => $chatId,
            'user_id' => $user->id
        ]);
        
        $meta->update(['archived' => !$meta->archived]);
        
        return response()->json([
            'status' => true,
            'archived' => $meta->archived
        ]);
    }

    public function getChatMessages($chatId)
    {
        $user = auth()->user();
        
        $chat = Chat::where('id', $chatId)
            ->where(function($query) use ($user) {
                $query->where('user_one_id', $user->id)
                      ->orWhere('user_two_id', $user->id);
            })
            ->firstOrFail();

        $messages = $chat->messages()
            ->with(['sender', 'attachments', 'reads'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'status' => true,
            'data' => $messages
        ]);
    }

    // ✅ زودي الـ method دي علشان mark all as read
    public function markAllAsRead($chatId)
    {
        $user = auth()->user();
        
        $chat = Chat::where('id', $chatId)
            ->where(function($query) use ($user) {
                $query->where('user_one_id', $user->id)
                      ->orWhere('user_two_id', $user->id);
            })
            ->firstOrFail();

        // Get unread messages
        $unreadMessages = Message::where('chat_id', $chatId)
            ->where('receiver_id', $user->id)
            ->whereDoesntHave('reads', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        foreach ($unreadMessages as $message) {
            MessageRead::firstOrCreate([
                'message_id' => $message->id,
                'user_id' => $user->id
            ], [
                'read_at' => now()
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'All messages marked as read',
            'marked_count' => $unreadMessages->count()
        ]);
    }
}