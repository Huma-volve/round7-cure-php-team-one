<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Arr;

class SupportTicketService
{
    public function createTicket(array $data, ?User $user = null): Ticket
    {
        $ticket = Ticket::create([
            'user_id' => $user?->id,
            'subject' => $data['subject'],
            'priority' => $data['priority'] ?? 'medium',
            'status' => Ticket::STATUS_OPEN,
            'contact_name' => $data['contact_name'] ?? $user?->name,
            'contact_email' => $data['contact_email'] ?? $user?->email,
            'contact_phone' => $data['contact_phone'] ?? $user?->mobile,
            'source' => $data['source'] ?? ($user ? 'in_app' : 'contact_form'),
        ]);

        $this->addMessage($ticket, Arr::get($data, 'message'), 'user', $user);

        return $ticket->fresh(['messages']);
    }

    public function addMessage(
        Ticket $ticket,
        string $body,
        string $senderType,
        ?User $sender = null,
        ?string $status = null
    ): TicketMessage {
        $message = $ticket->messages()->create([
            'sender_type' => $senderType,
            'sender_id' => $sender?->id,
            'message' => $body,
        ]);

        $ticket->last_reply_at = now();
        if ($status) {
            $ticket->status = $status;
        }
        $ticket->save();

        return $message;
    }
}

