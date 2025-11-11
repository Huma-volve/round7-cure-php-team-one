<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Services\Payment\PaymentMethodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PatientPaymentMethodController extends Controller
{
    public function __construct(private readonly PaymentMethodService $service)
    {
    }

    public function index(User $user): View
    {
        $methods = $user->paymentMethods()
            ->withTrashed()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.payment_methods.index', compact('user', 'methods'));
    }

    public function setDefault(User $user, PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->ensureMethodBelongsToUser($user, $paymentMethod);
        $this->authorize('update', $paymentMethod);

        $this->service->setDefault($paymentMethod, auth()->user());

        return back()->with('success', __('messages.payment_method.set_default'));
    }

    public function destroy(User $user, PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->ensureMethodBelongsToUser($user, $paymentMethod);
        $this->authorize('delete', $paymentMethod);

        $this->service->delete($paymentMethod, auth()->user());

        return back()->with('success', __('messages.payment_method.deleted'));
    }

    public function restore(User $user, int $paymentMethodId): RedirectResponse
    {
        $paymentMethod = PaymentMethod::withTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($paymentMethodId);

        $this->authorize('restore', $paymentMethod);

        $this->service->restore($paymentMethod, auth()->user());

        return back()->with('success', __('messages.payment_method.restored'));
    }

    protected function ensureMethodBelongsToUser(User $user, PaymentMethod $paymentMethod): void
    {
        abort_if($paymentMethod->user_id !== $user->id, 404);
    }
}

