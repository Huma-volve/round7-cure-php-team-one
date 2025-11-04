<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return $this->redirectToDashboard($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectToDashboard($user);
    }
    
    private function redirectToDashboard($user): RedirectResponse
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard').'?verified=1');
        }
        return redirect()->intended('/?verified=1');
    }
}
