<?php

namespace App\Http\Controllers;

use App\DTOs\Payment\CreatePaymentDTO;
use App\Http\Requests\CreatePaymentRequest;
use App\Http\Requests\ConfirmPaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentTestController extends Controller
{
    public function __construct(
        private readonly PaymentService $payments,
        private readonly PaymentRepository $repo
    ) {}

    public function index(Request $request): View
    {
        // Get some bookings for testing
        $bookings = Booking::with(['patient.user', 'doctor.user'])
            ->latest()
            ->limit(10)
            ->get();

        // Auto-confirm payment if payment_success parameter exists
        if ($request->has('payment_success')) {
            $sessionId = $request->get('session_id');
            $paymentId = $request->get('payment_id');
            
            if ($sessionId || $paymentId) {
                try {
                    $confirmId = $sessionId ?? $paymentId;
                    $resp = $this->payments->confirm('stripe', $confirmId, []);

                    // Update payment status
                    $payment = Payment::where('transaction_id', $confirmId)
                        ->orWhere('transaction_id', 'like', '%' . $confirmId . '%')
                        ->first();
                    
                    if (!$payment && $sessionId) {
                        // Try to find by booking_id from metadata
                        $payment = Payment::where('gateway', 'stripe')
                            ->where('status', 'pending')
                            ->latest()
                            ->first();
                    }
                    
                    if ($payment) {
                        $payment->update([
                            'status' => $resp->isSuccessful() ? 'success' : 'failed',
                            'transaction_id' => $resp->getPaymentId() ?? $payment->transaction_id,
                        ]);
                    }

                    return view('payment-test.index', compact('bookings'))->with([
                        'success' => 'تم الدفع بنجاح!',
                        'confirmation' => [
                            'status' => $resp->getStatus(),
                            'provider' => $resp->getProvider(),
                            'payment_id' => $resp->getPaymentId(),
                            'successful' => $resp->isSuccessful(),
                        ],
                    ]);
                } catch (\Exception $e) {
                    // Continue to show page even if confirmation fails
                }
            }
        }

        return view('payment-test.index', compact('bookings'));
    }

    public function createIntent(Request $request)
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'gateway' => ['required', 'in:stripe,paypal,cash'],
            'currency' => ['required', 'string', 'size:3'],
            'amount' => ['required', 'numeric', 'min:0.5'],
            'description' => ['nullable', 'string', 'max:255'],
            'return_url' => ['nullable', 'url', 'required_if:gateway,paypal'],
            'cancel_url' => ['nullable', 'url', 'required_if:gateway,paypal'],
        ]);

        try {
            $booking = Booking::with('patient')->findOrFail($request->booking_id);
            
            // Get user_id from patient if available
            $patientId = $booking->patient->user_id ?? $booking->patient_id ?? auth()->id() ?? 1;
            
            $dto = new CreatePaymentDTO(
                bookingId: (int) $request->booking_id,
                gateway: $request->gateway,
                currency: $request->currency,
                amount: (string) $request->amount,
                description: $request->description ?? "Test Payment for Booking #{$request->booking_id}",
                patientId: $patientId,
                metadata: ['booking_id' => $request->booking_id, 'test' => true],
                returnUrl: $request->return_url ?? url('/payment-test?payment_success=true&session_id={CHECKOUT_SESSION_ID}'),
                cancelUrl: $request->cancel_url ?? url('/payment-test?payment_cancelled=true'),
            );

            $resp = $this->payments->create($dto);

            $payment = $this->repo->create([
                'booking_id' => $request->booking_id,
                'amount' => $request->amount,
                'transaction_id' => $resp->getPaymentId() ?? uniqid('cash_'),
                'gateway' => $resp->getProvider(),
                'status' => $resp->isSuccessful() ? 'pending' : 'failed',
            ]);

            return back()->with([
                'success' => 'تم إنشاء عملية الدفع بنجاح!',
                'payment' => $payment,
                'payment_response' => [
                    'client_secret' => $resp->getClientSecret(),
                    'approve_url' => $resp->getApproveUrl(),
                    'payment_id' => $resp->getPaymentId(),
                    'payment_intent_id' => $resp->getPaymentId(), // Same as payment_id for Checkout Session
                    'status' => $resp->getStatus(),
                ],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'gateway' => ['required', 'in:stripe,paypal,cash'],
            'payment_id' => ['required', 'string'],
        ]);

        try {
            // Find payment first
            $payment = Payment::where('transaction_id', $request->payment_id)
                ->orWhere('transaction_id', 'like', '%' . $request->payment_id . '%')
                ->first();
            
            if (!$payment) {
                return back()->withErrors(['error' => 'Payment not found']);
            }
            
            // Cash payments don't need gateway confirmation
            if ($request->gateway === 'cash') {
                $payment->update(['status' => 'success']);
                
                return back()->with([
                    'success' => 'تم تأكيد الدفع النقدي بنجاح!',
                    'confirmation' => [
                        'status' => 'success',
                        'provider' => 'cash',
                        'payment_id' => $payment->transaction_id,
                        'successful' => true,
                    ],
                ]);
            }
            
            // For Stripe and PayPal, confirm with gateway
            $resp = $this->payments->confirm($request->gateway, $request->payment_id, []);

            if ($payment) {
                $payment->update([
                    'status' => $resp->isSuccessful() ? 'success' : 'failed',
                    'transaction_id' => $resp->getPaymentId() ?? $payment->transaction_id,
                ]);
            }

            return back()->with([
                'success' => 'تم تأكيد الدفع بنجاح!',
                'confirmation' => [
                    'status' => $resp->getStatus(),
                    'provider' => $resp->getProvider(),
                    'payment_id' => $resp->getPaymentId(),
                    'successful' => $resp->isSuccessful(),
                ],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }
}

