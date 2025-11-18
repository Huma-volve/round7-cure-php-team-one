<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreContactTicketRequest;
use App\Http\Resources\TicketResource;
use App\Services\SupportTicketService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly SupportTicketService $ticketService)
    {
    }

    public function store(StoreContactTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket([
            'subject' => $request->input('subject'),
            'priority' => $request->input('priority', 'medium'),
            'message' => $request->input('message'),
            'contact_name' => $request->input('name'),
            'contact_email' => $request->input('email'),
            'contact_phone' => $request->input('phone'),
            'source' => $request->input('source', 'contact_form'),
        ]);

        return $this->createdResponse(
            new TicketResource($ticket),
            'messages.ticket.created'
        );
    }
}

