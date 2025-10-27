<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'message' => $this->message,
            'file_url' => $this->file_url ? asset('storage/' . $this->file_url) : null,
            'is_read' => (bool) $this->is_read,
            'archived' => (bool) $this->archived,
            'favorite' => (bool) $this->favorite,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->toDateTimeString(),

            // ممكن تضيف هنا بيانات المستخدمين لو عايز تظهر الاسم والصورة مثلاً
            'sender' => [
                'id' => $this->sender?->id,
                'name' => $this->sender?->name,
                'avatar' => $this->sender?->avatar ?? null,
            ],
            'receiver' => [
                'id' => $this->receiver?->id,
                'name' => $this->receiver?->name,
                'avatar' => $this->receiver?->avatar ?? null,
            ],
        ];
        
    }
}
