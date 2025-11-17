<?php

namespace App\Http\Controllers\Api\Chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\ChatUserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// use Request;


class MessageController extends Controller
{



    public function getMessages($chatId)
    {
        $user = auth()->user();

        // جلب الشات للتأكد من أنه موجود والمستخدم طرف فيه
        $chat = \App\Models\Chat::where('id', $chatId)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->firstOrFail();

        // جلب كل الرسائل في الشات
        $messages = $chat->messages()->with('sender', 'receiver')->get();
        // تحديث الرسائل الغير مقروءة للمستخدم الحالي
        $chat->messages()
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'chat_id' => $chat->id,
            'messages' => $messages
        ]);
    }



    public function markAsRead(Request $request, $chatId)
    {
        $user = auth()->user();

        // جلب الرسائل الغير مقروءة للمستخدم الحالي في الشات
        $messages = \App\Models\Message::where('chat_id', $chatId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->get();

        // تعليم كل رسالة كمقروءة
        foreach ($messages as $message) {
            $message->read_at = now();
            $message->save();
        }

        return response()->json([
            'message' => 'Messages marked as read',
            'read_count' => $messages->count()
        ]);
    }


    public function send(SendMessageRequest $request)
    {
        $user = $request->user();
        $chatId = $request->input('chat_id');
        $receiverId = $request->input('receiver_id'); // هنستخدمها لو مفيش chat_id

        DB::beginTransaction();
        try {
            // ✅ إنشاء أو جلب الشات
            if ($chatId) {
                $chat = Chat::findOrFail($chatId);
            } else {
                if (!$receiverId) {
                    return response()->json(['error' => 'receiver_id is required when chat_id is missing'], 422);
                }

                if ($receiverId == $user->id) {
                    return response()->json(['error' => 'You cannot chat with yourself'], 400);
                }
$chat = Chat::where(function($q) use ($user, $receiverId) {
    $q->where('user_one_id', $user->id)
      ->where('user_two_id', $receiverId);
})->orWhere(function($q) use ($user, $receiverId) {
    $q->where('user_one_id', $receiverId)
      ->where('user_two_id', $user->id);
})->first();

if (!$chat) {
    $chat = Chat::create([
        'user_one_id' => $user->id,
        'user_two_id' => $receiverId,
        'last_message_at' => now(),
    ]);

    // meta لكل مستخدم
    ChatUserMeta::create(['chat_id' => $chat->id, 'user_id' => $user->id]);
    ChatUserMeta::create(['chat_id' => $chat->id, 'user_id' => $receiverId]);
}


                // meta records
                ChatUserMeta::firstOrCreate(['chat_id' => $chat->id, 'user_id' => $user->id]);
                ChatUserMeta::firstOrCreate(['chat_id' => $chat->id, 'user_id' => $receiverId]);
            }

            // ✅ معالجة المرفق
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
    'receiver_id' => $receiverId,
    'type' => $request->type,
    'body' => $request->body,
    'attachment_path' => $attachmentPath,
    'attachment_mime' => $attachmentMime,
    'attachment_size' => $attachmentSize,
]);


            // ✅ تحديث بيانات الشات
            $chat->update([
                'last_message' => $request->filled('body')
                    ? Str::limit($request->body, 500)
                    : ($attachmentPath ? 'Attachment' : null),
                'last_message_id' => $message->id,
                'last_message_at' => $message->created_at,
            ]);

            DB::commit();

        return response()->json([
    'chat_id' => $chat->id,
    'message' => [
        'id' => $message->id,
        'body' => $message->body,
        'type' => $message->type,
        'attachment_url' => $attachmentPath
            ? Storage::disk('public')->url($attachmentPath)
            : null,
        'created_at' => $message->created_at,
    ],
]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $id)
    {
        // dd($request);
        $message = Message::findOrFail($id);

        // تأكيد إن المستخدم هو صاحب الرسالة
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['error' => 'غير مصرح لك بتعديل هذه الرسالة'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message->update([
            'body' => $request->body,
        ]);

        return response()->json([
            'message' => 'تم تحديث الرسالة بنجاح',
            'data' => $message,
        ]);
    }

    public function destroy($id)
{
    $message = Message::findOrFail($id);

    // نفس شرط الأمان
    if ($message->sender_id !== auth()->id()) {
        return response()->json(['error' => 'غير مصرح لك بحذف هذه الرسالة'], 403);
    }

    $message->delete();

    return response()->json([
        'message' => 'تم حذف الرسالة بنجاح'
    ]);
}








}
