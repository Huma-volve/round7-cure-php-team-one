<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Spatie\Activitylog\Facades\Activity;

class AccountController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        return view('admin.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->profile_image_path = $path;
        }

        $user->save();

        Activity::causedBy($user)->performedOn($user)
            ->withProperties(['ip' => $request->ip()])
            ->log('admin_profile_updated');

        return back()->with('status', __('Profile updated successfully'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('admin.account.settings', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => __('The current password is incorrect')]);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        Activity::causedBy($user)->performedOn($user)
            ->withProperties(['ip' => $request->ip()])
            ->log('admin_password_changed');

        return back()->with('status', __('Password updated successfully'));
    }

    public function updateLanguage(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'preferred_locale' => ['required', 'in:en,ar'],
        ]);

        $user->preferred_locale = $validated['preferred_locale'];
        $user->save();

        Activity::causedBy($user)->performedOn($user)
            ->withProperties([
                'ip' => $request->ip(),
                'preferred_locale' => $validated['preferred_locale'],
            ])
            ->log('admin_language_changed');

        return back()->with('status', __('Language updated successfully'));
    }

    public function activityLog(Request $request)
    {
        $user = Auth::user();
        
        $logs = ActivityModel::where('causer_type', get_class($user))
            ->where('causer_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('admin.account.activity_log', compact('logs'));
    }
}


