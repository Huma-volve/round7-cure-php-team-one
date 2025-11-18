<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public TicketMessage $message
    ) {
    }

    public function build(): self
    {
        return $this->subject('رد جديد على تذكرتك - ' . config('app.name'))
            ->view('emails.tickets.reply');
    }
}

