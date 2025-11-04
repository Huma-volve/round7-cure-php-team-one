<?php
namespace App\Http\Controllers;

 use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user = auth()->user();
    //     $role = $user->getRoleNames()->first(); // مثلاً "doctor" أو "patient"

    //     // ✅ لو تم إرسال receiver_id، تأكد من وجود أو إنشاء الشات
    //     if ($request->has('receiver_id')) {
    //         $receiverId = $request->receiver_id;

    //         $chat = Chat::where(function ($q) use ($user, $receiverId) {
    //                 $q->where('user_one_id', $user->id)
    //                   ->where('user_two_id', $receiverId);
    //             })
    //             ->orWhere(function ($q) use ($user, $receiverId) {
    //                 $q->where('user_one_id', $receiverId)
    //                   ->where('user_two_id', $user->id);
    //             })
    //             ->first();

    //         // ✅ لو مش موجود، أنشئ شات جديد
    //         if (!$chat) {
    //             $chat = Chat::create([
    //                 'user_one_id' => $user->id,
    //                 'user_two_id' => $receiverId,
    //                 'last_message' => null,
    //                 'last_message_at' => now(),
    //             ]);
    //         }
    //     }

    //     // ✅ استرجاع الشاتات اللي المستخدم طرف فيها
    //     $chats = Chat::where('user_one_id', $user->id)
    //         ->orWhere('user_two_id', $user->id)
    //         ->with(['userOne', 'userTwo', 'messages'])
    //         ->latest('last_message_at')
    //         ->get();

    //     // ✅ لو المستخدم patient -> رجع الدكاترة اللي اتكلم معاهم
    //     if ($role === 'patient') {
    //         $relatedUsers = $chats->map(function ($chat) use ($user) {
    //             return $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;
    //         })->unique('id')->values();
    //     }
    //     // ✅ لو المستخدم doctor -> رجع المرضى اللي اتكلم معاهم
    //     elseif ($role === 'doctor') {
    //         $relatedUsers = $chats->map(function ($chat) use ($user) {
    //             return $chat->user_one_id === $user->id ? $chat->userTwo : $chat->userOne;
    //         })->unique('id')->values();
    //     } else {
    //         $relatedUsers = collect();
    //     }

    //     return response()->json([
    //         'chats' => $chats,
    //         'related_users' => $relatedUsers,
    //     ]);
    // }




    public function index(Request $request)
{
    // ✅ المستخدم الحالي
    $user = auth()->user();
    $role = $user->getRoleNames()->first(); // "doctor" أو "patient"
    // dd($user);

    // 🩺 الحالة 1: لو المستخدم Patient → رجع كل الدكاترة
    if ($role === 'patient' && !$request->has('history') && !$request->has('favorite')) {
        $doctors = User::role('doctor')
            ->select('id', 'name', 'email')
            ->get();

        return response()->json([
            'status' => true,
            'type' => 'doctors_list',
            'data' => $doctors,
        ]);
    }

    // 💬 الحالة 2 + 3: History أو Favorite Chats
    $query = Chat::query()
        ->where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)
              ->orWhere('user_two_id', $user->id);
        })
        ->with(['userOne', 'userTwo'])
        ->latest('last_message_at');

    // ⭐ لو المستخدم طالب الفيفوريت فقط → استخدم جدول chat_user_meta
    if ($request->boolean('favorite')) {
        $query->whereHas('meta', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('favorite', true);
        });
    }

    $chats = $query->get();

    // ✅ نوع الريسبونس حسب الفلتر
    $type = match (true) {
        $request->boolean('favorite') => 'favorite_chats',
        $request->has('history')      => 'chat_history',
        default                       => 'chat_history',
    };

    return response()->json([
        'status' => true,
        'type' => $type,
        'data' => $chats,
    ]);
}
}
