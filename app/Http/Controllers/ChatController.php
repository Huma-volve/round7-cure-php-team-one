<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\SendMessageRequest;
use App\Models\Chat;
use App\Models\ChatUserMeta;
use App\Models\Message;
use App\Models\User;
use DB;
// use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Request;

use Storage;
use Str;

class ChatController extends Controller
{
    public function createChat(Request $request)
    {
        $user = auth()->user();
        $receiverId = $request->input('receiver_id');

        if ($user->id == $receiverId) {
            return response()->json(['error' => 'You cannot create a chat with yourself'], 400);
        }

        $chat = Chat::where(function ($query) use ($user, $receiverId) {
            $query->where('user_one_id', $user->id)
                ->where('user_two_id', $receiverId);
        })
            ->orWhere(function ($query) use ($user, $receiverId) {
                $query->where('user_one_id', $receiverId)
                    ->where('user_two_id', $user->id);
            })
            ->first();

        if ($chat) {
            $chat->load(['userOne', 'userTwo', 'meta' => fn($q) => $q->where('user_id', $user->id)]);
            return response()->json(['chat' => $chat, 'message' => 'Chat already exists']);
        }

        // ✅ إنشاء الشات
        $chat = Chat::create([
            'user_one_id' => $user->id,
            'user_two_id' => $receiverId,
            'last_message_at' => now(),
        ]);

        // ✅ إنشاء meta record لكل مستخدم
        ChatUserMeta::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ]);

        ChatUserMeta::create([
            'chat_id' => $chat->id,
            'user_id' => $receiverId,
        ]);

        $chat->load(['userOne', 'userTwo', 'meta' => fn($q) => $q->where('user_id', $user->id)]);

        return response()->json(['chat' => $chat, 'message' => 'Chat created successfully']);
    }





    // public function send(SendMessageRequest $request)
    // {
    //     $user = $request->user();
    //     $receiverId = $request->input('receiver_id');

    //     // ✅ 1. التحقق إن المستخدم مش بيبعت لنفسه
    //     if ($user->id == $receiverId) {
    //         return response()->json(['error' => 'You cannot send a message to yourself'], 400);
    //     }

    //     // ✅ 2. البحث عن الشات بين المستخدمين (في الاتجاهين)
    //     $chat = Chat::where(function ($query) use ($user, $receiverId) {
    //             $query->where('user_one_id', $user->id)
    //                   ->where('user_two_id', $receiverId);
    //         })
    //         ->orWhere(function ($query) use ($user, $receiverId) {
    //             $query->where('user_one_id', $receiverId)
    //                   ->where('user_two_id', $user->id);
    //         })
    //         ->first();

    //     // ✅ 3. لو الشات مش موجود، نعمل إنشاء جديد
    //     if (!$chat) {
    //         $chat = Chat::create([
    //             'user_one_id' => $user->id,
    //             'user_two_id' => $receiverId,
    //             'last_message_at' => now(),
    //         ]);

    //         // نضمن وجود ChatUserMeta لكل مستخدم
    //         foreach ([$user->id, $receiverId] as $participantId) {
    //             ChatUserMeta::firstOrCreate(
    //                 ['chat_id' => $chat->id, 'user_id' => $participantId],
    //                 ['favorite' => false, 'archived' => false, 'muted' => false]
    //             );
    //         }
    //     }

    //     // ✅ 4. إنشاء الرسالة داخل Transaction
    //     $message = null;
    //     DB::transaction(function () use ($request, $user, $chat, &$message) {
    //         $attachmentPath = null;
    //         $attachmentMime = null;
    //         $attachmentSize = null;

    //         if ($request->hasFile('attachment')) {
    //             $file = $request->file('attachment');
    //             $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
    //             $attachmentPath = $file->storeAs('chat_attachments', $filename, 'public');
    //             $attachmentMime = $file->getClientMimeType();
    //             $attachmentSize = $file->getSize();
    //         }

    //         $message = Message::create([
    //             'chat_id' => $chat->id,
    //             'sender_id' => $user->id,
    //             'receiver_id' => $request->input('receiver_id'),
    //             'type' => $request->type,
    //             'body' => $request->body,
    //             'attachment_path' => $attachmentPath,
    //             'attachment_mime' => $attachmentMime,
    //             'attachment_size' => $attachmentSize,
    //             'status' => 'sent',
    //         ]);

    //         // تحديث بيانات الشات
    //         $chat->update([
    //             'last_message' => $request->filled('body') 
    //                 ? Str::limit($request->body, 500) 
    //                 : ($attachmentPath ? 'Attachment' : null),
    //             'last_message_id' => $message->id,
    //             'last_message_at' => $message->created_at,
    //         ]);
    //     });

    //     // ✅ 5. تحضير الـ Response
    //     $payload = [
    //         'chat' => [
    //             'id' => $chat->id,
    //             'user_one_id' => $chat->user_one_id,
    //             'user_two_id' => $chat->user_two_id,
    //             'last_message' => $chat->last_message,
    //             'last_message_at' => $chat->last_message_at,
    //         ],
    //         'message' => [
    //             'id' => $message->id,
    //             'chat_id' => $chat->id,
    //             'sender_id' => $message->sender_id,
    //             'receiver_id' => $message->receiver_id,
    //             'type' => $message->type,
    //             'body' => $message->body,
    //             'attachment_url' => $message->attachment_path 
    //                 ? Storage::disk('public')->url($message->attachment_path) 
    //                 : null,
    //             'status' => $message->status,
    //             'created_at' => $message->created_at->toDateTimeString(),
    //         ]
    //     ];

    //     // ✅ 6. إرسال الحدث (Broadcast)
    //     event(new MessageSent($payload));

    //     return response()->json($payload, 201);
    // }

    public function chatList(Request $request)
    {
        $user = auth()->user();

        $favorite = $request->query('favorite'); // true/false/null
        $archived = $request->query('archived'); // true/false/null

        $chats = Chat::where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)
                ->orWhere('user_two_id', $user->id);
        })
            ->whereHas('meta', function ($q) use ($user, $favorite, $archived) {
                $q->where('user_id', $user->id);

                // لو favorite=true في query
                if ($favorite === 'true') {
                    $q->where('favorite', true);
                }

                // لو archived=true في query
                if ($archived === 'true') {
                    $q->where('archived', true);
                }
            })
            ->with([
                'userOne',
                'userTwo',
                'messages' => function ($query) {
                    $query->latest()->limit(1); // آخر رسالة فقط
                },
                'meta' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }
            ])
            ->get();

        if ($chats->isEmpty()) {
            return response()->json(['message' => 'empty']);
        }

        $chatList = $chats->map(function ($chat) use ($user) {
            $receiver = $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;

            $unreadCount = $chat->messages()
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();

            return [
                'chat_id' => $chat->id,
                'user' => $user,
                'receiver' => $receiver,
                'last_message' => $chat->messages->first() ? [
                    'id' => $chat->messages->first()->id,
                    'body' => $chat->messages->first()->body,
                    'created_at' => $chat->messages->first()->created_at,
                ] : null,
                'unread_count' => $unreadCount,
                'meta' => $chat->meta->first() ? [
                    'favorite' => $chat->meta->first()->favorite,
                    'archived' => $chat->meta->first()->archived,
                    'muted' => $chat->meta->first()->muted,
                ] : [
                    'favorite' => false,
                    'archived' => false,
                    'muted' => false,
                ],
            ];
        });

        return response()->json([
            'message' => 'success',
            'data' => $chatList
        ]);
    }





    public function toggleFavorite($chatId)
    {
        $user = auth()->user();

        $meta = ChatUserMeta::where('chat_id', $chatId)
            ->where('user_id', $user->id)
            ->first();

        if (! $meta) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        $meta->update(['favorite' => ! $meta->favorite]);

        return response()->json([
            'message' => $meta->favorite ? 'Chat added to favorites' : 'Chat removed from favorites',
            'meta' => $meta
        ]);
    }
    public function toggleArchive($chatId)
    {
        $user = auth()->user();

        $meta = ChatUserMeta::where('chat_id', $chatId)
            ->where('user_id', $user->id)
            ->first();

        if (! $meta) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        $meta->update(['archived' => ! $meta->archived]);

        return response()->json([
            'message' => $meta->archived ? 'Chat added to archived' : 'Chat removed from archived',
            'meta' => $meta
        ]);
    }

    public function searchChats(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('name');

        if (!$search) {
            return response()->json(['error' => 'Please provide a name to search'], 400);
        }

        // البحث داخل قاعدة البيانات نفسها (مش في PHP)
        $chats = Chat::where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)
                ->orWhere('user_two_id', $user->id);
        })
            ->where(function ($q) use ($search, $user) {
                $q->whereHas('userOne', function ($query) use ($search, $user) {
                    $query->where('id', '!=', $user->id)
                        ->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('userTwo', function ($query) use ($search, $user) {
                        $query->where('id', '!=', $user->id)
                            ->where('name', 'like', "%{$search}%");
                    });
            })
            ->with([
                'userOne:id,name,email',
                'userTwo:id,name,email',
                'messages' => function ($q) {
                    $q->latest()->limit(1); // آخر رسالة فقط
                }
            ])
            ->get();

        if ($chats->isEmpty()) {
            return response()->json(['message' => 'No chats found for this name']);
        }

        // ممكن تضيف unread count كمان هنا
        $chats->each(function ($chat) use ($user) {
            $chat->unread_count = $chat->messages()
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();
        });

        return response()->json(['chats' => $chats]);
    }



    public function destroy($id)
    {
        $user = auth()->user();

        $chat = Chat::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->firstOrFail();

        // soft delete messages
        $chat->messages()->delete();

        // soft delete chat_user_meta
        $chat->meta()->delete();

        // soft delete chat itself
        $chat->delete();

        return response()->json([
            'message' => 'Chat deleted successfully'
        ]);
    }
}
