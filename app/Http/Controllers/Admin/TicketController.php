<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReplyTicketRequest;
use App\Http\Requests\Admin\UpdateTicketStatusRequest;
use App\Mail\TicketReplyMail;
use App\Models\Ticket;
use App\Models\User;
use App\Services\SupportTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(private readonly SupportTicketService $ticketService)
    {
    }

    public function index(Request $request): View
    {
        $query = Ticket::with(['user', 'assignedAdmin']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        $tickets = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'assignedAdmin', 'messages.sender']);
        $admins = User::role('admin')->get(['id', 'name']);

        return view('admin.tickets.show', compact('ticket', 'admins'));
    }

    public function reply(ReplyTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $message = $this->ticketService->addMessage(
            $ticket,
            $request->input('message'),
            'admin',
            $request->user(),
            $request->input('status', Ticket::STATUS_PENDING)
        );

        $ticket->assigned_admin_id = $ticket->assigned_admin_id ?? $request->user()->id;
        $ticket->save();

        $recipient = $ticket->contact_email ?? $ticket->user?->email;

        if ($recipient) {
            Mail::to($recipient)->send(new TicketReplyMail($ticket, $message));
        }

        return redirect()
            ->back()
            ->with('success', __('messages.ticket.reply_sent'));
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->status = $request->input('status');
        $ticket->assigned_admin_id = $request->input('assigned_admin_id', $ticket->assigned_admin_id);
        $ticket->closed_at = $ticket->status === Ticket::STATUS_CLOSED ? now() : null;
        $ticket->save();

        return redirect()
            ->back()
            ->with('success', __('messages.ticket.status_updated'));
    }
}


