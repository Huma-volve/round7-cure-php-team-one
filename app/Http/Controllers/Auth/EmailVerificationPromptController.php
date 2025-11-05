<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended('/');
        }
        
        return view('auth.verify-email');
    }
}
