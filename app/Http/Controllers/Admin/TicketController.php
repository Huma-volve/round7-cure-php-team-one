<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'assignedAdmin']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        $tickets = $query->orderByDesc('id')->paginate(15);
        return response()->json($tickets);
    }

    public function show(int $id)
    {
        $ticket = Ticket::with(['user', 'assignedAdmin', 'messages'])->findOrFail($id);
        return response()->json($ticket);
    }
}


