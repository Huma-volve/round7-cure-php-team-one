<?php

namespace App\Http\Controllers;

use App\DTOs\Payment\CreatePaymentDTO;
use App\Models\Booking;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentTestController extends Controller
{
    public function __construct(
        private readonly PaymentService $payments,
        private readonly PaymentRepository $repo
    ) {
    }

    public function index(): View
    {
        $bookings = Booking::with(['patient.user'])
            ->latest('date_time')
            ->take(25)
            ->get();

        return view('payment-test.index', compact('bookings'));
    }

    public function createIntent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'gateway' => ['required', 'in:stripe,paypal,cash'],
            'currency' => ['required', 'string', 'max:10'],
            'amount' => ['required', 'numeric', 'min:0.5'],
            'description' => ['nullable', 'string', 'max:255'],
            'return_url' => ['nullable', 'url'],
            'cancel_url' => ['nullable', 'url'],
        ]);

        if (in_array($validated['gateway'], ['stripe', 'paypal'], true)) {
            $request->validate([
                'return_url' => ['required', 'url'],
                'cancel_url' => ['required', 'url'],
            ]);
        }

        $booking = Booking::with('patient')->findOrFail($validated['booking_id']);
        $patientId = $booking->patient?->user_id ?? Auth::id();

        $dto = new CreatePaymentDTO(
            bookingId: (int) $booking->id,
            gateway: $validated['gateway'],
            currency: $validated['currency'],
            amount: (string) $validated['amount'],
            description: $validated['description'],
            patientId: $patientId,
            metadata: [
                'booking_id' => $booking->id,
                'initiated_by' => Auth::id(),
            ],
            returnUrl: $validated['return_url'] ?? $request->fullUrlWithQuery(['payment_success' => 'true']),
            cancelUrl: $validated['cancel_url'] ?? $request->fullUrlWithQuery(['payment_cancelled' => 'true']),
        );

        try {
            $resp = $this->payments->create($dto);
        } catch (\Throwable $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        $payment = $this->repo->create([
            'booking_id' => $booking->id,
            'amount' => $validated['amount'],
            'transaction_id' => $resp->getPaymentId() ?? uniqid('cash_'),
            'gateway' => $resp->getProvider(),
            'status' => $resp->isSuccessful() ? 'pending' : 'failed',
        ]);

        $payment->setAttribute('currency', $validated['currency']);
        $payment->setAttribute('description', $validated['description']);

        session()->flash('payment', $payment);
        session()->flash('payment_response', [
            'payment_id' => $resp->getPaymentId() ?? $payment->transaction_id,
            'status' => $resp->getStatus() ?? ($resp->isSuccessful() ? 'pending' : 'failed'),
            'approve_url' => $resp->getApproveUrl(),
            'client_secret' => $resp->getClientSecret(),
            'payment_intent_id' => $resp->getPaymentId(),
        ]);

        return redirect()
            ->route('payment-test.index')
            ->with('success', __('تم إنشاء عملية الدفع بنجاح.'));
    }

    public function confirm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gateway' => ['required', 'in:stripe,paypal,cash'],
            'payment_id' => ['required', 'string'],
        ]);

        $payment = Payment::where('transaction_id', $validated['payment_id'])->first();

        if (!$payment) {
            return back()->withErrors(__('لم يتم العثور على عملية الدفع.'));
        }

        if ($validated['gateway'] === 'cash') {
            $payment->update(['status' => 'success']);

            session()->flash('confirmation', [
                'status' => 'success',
                'provider' => 'cash',
                'payment_id' => $payment->transaction_id,
                'successful' => true,
            ]);

            session()->flash('payment', $payment->fresh());

            return redirect()->route('payment-test.index')->with('success', __('تم تأكيد الدفع النقدي.'));
        }

        try {
            $resp = $this->payments->confirm($validated['gateway'], $validated['payment_id'], []);
        } catch (\Throwable $e) {
            return back()->withErrors($e->getMessage());
        }

        $payment->update([
            'status' => $resp->isSuccessful() ? 'success' : 'failed',
            'transaction_id' => $resp->getPaymentId() ?? $payment->transaction_id,
        ]);

        session()->flash('confirmation', [
            'status' => $resp->getStatus(),
            'provider' => $resp->getProvider(),
            'payment_id' => $resp->getPaymentId(),
            'successful' => $resp->isSuccessful(),
        ]);

        session()->flash('payment', $payment->fresh());

        return redirect()->route('payment-test.index')->with(
            'success',
            $resp->isSuccessful() ? __('تم تأكيد الدفع بنجاح.') : __('فشل تأكيد الدفع.')
        );
    }
}

