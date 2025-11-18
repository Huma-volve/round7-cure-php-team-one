<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReplySupportTicketRequest;
use App\Http\Requests\Api\StoreSupportTicketRequest;
use App\Http\Resources\TicketMessageResource;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\SupportTicketService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly SupportTicketService $ticketService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $tickets = Ticket::ownedBy($request->user())
            ->latest()
            ->paginate(10);

        return $this->paginatedResponse(TicketResource::collection($tickets));
    }

    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            new TicketResource($ticket->load('messages')),
            'messages.success'
        );
    }

    public function store(StoreSupportTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket([
            'subject' => $request->input('subject'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'),
            'source' => 'in_app',
        ], $request->user());

        return $this->createdResponse(
            new TicketResource($ticket),
            'messages.ticket.created'
        );
    }

    public function reply(ReplySupportTicketRequest $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id) {
            return $this->unauthorizedResponse();
        }

        $message = $this->ticketService->addMessage(
            $ticket,
            $request->input('message'),
            'user',
            $request->user(),
            Ticket::STATUS_OPEN
        );

        return $this->successResponse(
            new TicketMessageResource($message),
            'messages.ticket.reply_sent'
        );
    }
}

