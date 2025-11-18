<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        // If user is already authenticated, redirect based on role
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            if ($user->hasRole('admin', 'web')) {
                return redirect()->route('admin.dashboard');
            }
            
            if ($user->hasRole('doctor', 'web') && $user->doctor) {
                return redirect()->route('doctor.dashboard');
            }
            
            // If authenticated but no valid role, logout and show login
            Auth::guard('web')->logout();
        }
        
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        // Log للـ debugging
        \Log::info('User logged in', [
            'user_id' => $user->id,
            'roles' => $user->getRoleNames(),
        ]);

        // Admin redirect
        if ($user->hasRole('admin', 'web')) {
            return redirect()->route('admin.dashboard');
        }

        // Doctor redirect
        if ($user->hasRole('doctor', 'web')) {
            if (!$user->doctor) {
                Auth::guard('web')->logout();
                \Log::warning('Doctor login failed - no doctor record', ['user_id' => $user->id]);
                
                return redirect()->route('login')->withErrors([
                    'email' => 'المستخدم ليس لديه ملف طبيب. يرجى التواصل مع الإدارة.',
                ]);
            }

            return redirect()->route('doctor.dashboard');
        }

        // Default redirect - users without admin/doctor role are not allowed
        \Log::warning('User logged in without admin/doctor role', [
            'user_id' => $user->id,
            'roles' => $user->getRoleNames(),
        ]);
        
        Auth::guard('web')->logout();
        return redirect()->route('login')->withErrors([
            'email' => 'ليس لديك صلاحيات للدخول إلى لوحة التحكم.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
