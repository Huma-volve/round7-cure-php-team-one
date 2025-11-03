<?php

namespace App\Http\Controllers\Api\Chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\ChatUserMeta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{



 public function index(Chat $chat)
    {
        // تأكد إن المستخدم طرف في الشات قبل ما تعرض الرسائل
        $userId = Auth::id();

        if ($chat->user_one_id !== $userId && $chat->user_two_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // عرض الرسائل مع بيانات المرسل
        $messages = Message::where('chat_id', $chat->id)
            ->with('sender:id,name,email')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }




    /**
     * Send a message (text or attachment) in an existing chat.
     */
    public function send(SendMessageRequest $request)
    {
        $user = $request->user();
        $chat = Chat::findOrFail($request->chat_id);

        // create message inside transaction
        $message = null;
        DB::transaction(function () use ($request, $user, $chat, &$message) {
            $attachmentPath = null;
            $attachmentMime = null;
            $attachmentSize = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $attachmentPath = $file->storeAs('chat_attachments', $filename, 'public');
                $attachmentMime = $file->getClientMimeType();
                $attachmentSize = $file->getSize();
            }

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'type' => $request->type,
                'body' => $request->body,
                'attachment_path' => $attachmentPath,
                'attachment_mime' => $attachmentMime,
                'attachment_size' => $attachmentSize,
                'status' => 'sent',
            ]);

            // update chat summary fields
            $chat->last_message = $request->filled('body') ? Str::limit($request->body, 500) : ($attachmentPath ? 'Attachment' : null);
            $chat->last_message_id = $message->id;
            $chat->last_message_at = $message->created_at;
            $chat->save();

            // ensure chat_user_meta exists for both participants
            foreach ([$chat->user_one_id, $chat->user_two_id] as $participantId) {
                ChatUserMeta::firstOrCreate(
                    ['chat_id' => $chat->id, 'user_id' => $participantId],
                    ['favorite' => false, 'archived' => false, 'muted' => false]
                );
            }
        });

        // prepare response payload
        $payload = [
            'id' => $message->id,
            'chat_id' => $chat->id,
            'sender_id' => $message->sender_id,
            'type' => $message->type,
            'body' => $message->body,
            'attachment_url' => $message->attachment_path ? Storage::disk('public')->url($message->attachment_path) : null,
            'attachment_mime' => $message->attachment_mime,
            'attachment_size' => $message->attachment_size,
            'status' => $message->status,
            'created_at' => $message->created_at->toDateTimeString(),
        ];

        // TODO: broadcast event (MessageSent)  
event(new MessageSent($payload));
// أو dispatch عبر facade لو حبيت:
// MessageSent::dispatch($payload);
        return response()->json($payload, 201);
    }

    /**
     * Mark messages as read for the current user (store message_reads records).
     */
    public function markRead(\App\Http\Requests\MarkReadRequest $request)
    {
        $user = $request->user();
        $chat = Chat::findOrFail($request->chat_id);
        $messageIds = $request->message_ids;

        $now = now();

        foreach ($messageIds as $mid) {
            \App\Models\MessageRead::firstOrCreate(
                ['message_id' => $mid, 'user_id' => $user->id],
                ['read_at' => $now]
            );
        }

        // update chat_user_meta last_read_message_id
        $maxId = max($messageIds);
        \App\Models\ChatUserMeta::updateOrCreate(
            ['chat_id' => $chat->id, 'user_id' => $user->id],
            ['last_read_message_id' => $maxId]
        );

        // Optionally update messages.status if the other side has read/delivered logic
        // (we will implement more robust logic in next steps)

        return response()->json(['ok' => true]);
    }

   public function show($chatId)
{
    $messages = Message::where('chat_id', $chatId)
                       ->with('sender:id,name')
                       ->orderBy('created_at', 'asc')
                       ->get();

    return response()->json($messages);
}
public function store(SendMessageRequest $request, Chat $chat)
{
    $request->merge(['chat_id' => $chat->id]);
    return $this->send($request);
}

}
