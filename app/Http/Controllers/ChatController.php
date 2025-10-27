<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\StoreChatRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Storage;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $chats = Chat::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest()
            ->paginate(20);

        return ChatResource::collection($chats);
    }
 
  
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChatRequest $request)
    {
        $data = $request->validated();

       
        if ($request->hasFile('file_url')) {
            $filePath = $request->file('file_url')->store('chats', 'public');
            $data['file_url'] = $filePath;
        }

        $chat = Chat::create($data);

      
        return new ChatResource($chat);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        return new ChatResource($chat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        $request->validate([
            'is_read' => 'boolean',
            'archived' => 'boolean',
            'favorite' => 'boolean',
        ]);

        $chat->update($request->only(['is_read', 'archived', 'favorite']));

        return new ChatResource($chat);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        // حذف الملف لو موجود
        if ($chat->file_url) {
            Storage::disk('public')->delete($chat->file_url);
        }

        $chat->delete();

        return response()->json(['message' => 'Message deleted successfully.']);
    }
}
