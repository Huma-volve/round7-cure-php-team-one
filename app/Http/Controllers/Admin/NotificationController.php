<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAllRead(): RedirectResponse
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Redirect based on user role
        if ($user->hasRole('admin', 'web')) {
            return redirect()->route('admin.notifications.index')
                ->with('success', __('All notifications marked as read.'));
        }
        
        if ($user->hasRole('doctor', 'web')) {
            return redirect()->route('doctor.notifications.index')
                ->with('success', __('All notifications marked as read.'));
        }

        return redirect()->back()
            ->with('success', __('All notifications marked as read.'));
    }
}


