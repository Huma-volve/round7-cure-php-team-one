<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'priority' => $this->priority,
            'status' => $this->status,
            'source' => $this->source,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'last_reply_at' => $this->last_reply_at?->toIso8601String(),
            'messages' => TicketMessageResource::collection($this->whenLoaded('messages')),
        ];
    }
}

