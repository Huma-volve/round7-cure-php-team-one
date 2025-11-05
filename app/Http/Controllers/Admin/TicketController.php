<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
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

    public function show(int $id): View
    {
        $ticket = Ticket::with(['user', 'assignedAdmin', 'messages'])->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }
}


