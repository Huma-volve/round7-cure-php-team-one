<?php
namespace App\Http\Controllers;

 use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first(); // مثلاً "doctor" أو "patient"

        // ✅ لو تم إرسال receiver_id، تأكد من وجود أو إنشاء الشات
        if ($request->has('receiver_id')) {
            $receiverId = $request->receiver_id;

            $chat = Chat::where(function ($q) use ($user, $receiverId) {
                    $q->where('user_one_id', $user->id)
                      ->where('user_two_id', $receiverId);
                })
                ->orWhere(function ($q) use ($user, $receiverId) {
                    $q->where('user_one_id', $receiverId)
                      ->where('user_two_id', $user->id);
                })
                ->first();

            // ✅ لو مش موجود، أنشئ شات جديد
            if (!$chat) {
                $chat = Chat::create([
                    'user_one_id' => $user->id,
                    'user_two_id' => $receiverId,
                    'last_message' => null,
                    'last_message_at' => now(),
                ]);
            }
        }

        // ✅ استرجاع الشاتات اللي المستخدم طرف فيها
        $chats = Chat::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'messages'])
            ->latest('last_message_at')
            ->get();

        // ✅ لو المستخدم patient -> رجع الدكاترة اللي اتكلم معاهم
        if ($role === 'patient') {
            $relatedUsers = $chats->map(function ($chat) use ($user) {
                return $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;
            })->unique('id')->values();
        }
        // ✅ لو المستخدم doctor -> رجع المرضى اللي اتكلم معاهم
        elseif ($role === 'doctor') {
            $relatedUsers = $chats->map(function ($chat) use ($user) {
                return $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;
            })->unique('id')->values();
        } else {
            $relatedUsers = collect();
        }

        return response()->json([
            'chats' => $chats,
            'related_users' => $relatedUsers,
        ]);
    }
}
