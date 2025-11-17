<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Payment\CreatePaymentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentRequest;
use App\Http\Requests\ConfirmPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Services\Payment\PaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly PaymentService $payments,
        private readonly PaymentRepository $repo
    ) {}

    public function createIntent(CreatePaymentRequest $request): JsonResponse
    {
        $dto = new CreatePaymentDTO(
            bookingId: (int) $request->booking_id,
            gateway: $request->gateway,
            currency: $request->currency,
            amount: (string) $request->amount,
            description: $request->description,
            patientId: auth()->id(),
            metadata: ['booking_id' => $request->booking_id],
            returnUrl: $request->return_url,
            cancelUrl: $request->cancel_url,
        );

        $resp = $this->payments->create($dto);

        $payment = $this->repo->create([
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
            'transaction_id' => $resp->getPaymentId() ?? uniqid('cash_'),
            'gateway' => $resp->getProvider(),
            'status' => $resp->isSuccessful() ? 'pending' : 'failed',
        ]);

        // Add payment gateway response data (approve_url, client_secret, etc.)
        $paymentData = (new PaymentResource($payment))->toArray($request);
        $paymentData['approve_url'] = $resp->getApproveUrl(); // For Stripe Checkout & PayPal
        $paymentData['client_secret'] = $resp->getClientSecret(); // For Stripe Elements (if needed)
        $paymentData['payment_intent_id'] = $resp->getPaymentId(); // Stripe Payment Intent or Checkout Session ID

        return $this->createdResponse($paymentData, 'messages.payment.created');
    }

    public function confirm(ConfirmPaymentRequest $request): JsonResponse
    {
        // Find payment first
        $payment = Payment::where('transaction_id', $request->payment_id)
            ->orWhere('transaction_id', 'like', '%' . $request->payment_id . '%')
            ->first();
        
        if (!$payment) {
            return $this->notFoundResponse('messages.payment.not_found');
        }
        
        // Cash payments don't need gateway confirmation, just update status
        if ($request->gateway === 'cash') {
            $payment->update([
                'status' => 'success', // Cash is confirmed when received
            ]);
            
            return $this->successResponse([
                'status' => 'success',
                'provider' => 'cash',
                'payment_id' => $payment->transaction_id,
                'successful' => true,
                'payment_updated' => true,
            ], 'messages.payment.confirmed');
        }
        
        // For Stripe and PayPal, confirm with gateway
        $resp = $this->payments->confirm($request->gateway, $request->payment_id, []);
        
        if ($payment) {
            $payment->update([
                'status' => $resp->isSuccessful() ? 'success' : 'failed',
                'transaction_id' => $resp->getPaymentId() ?? $payment->transaction_id,
            ]);
        }
        
        return $this->successResponse([
            'status' => $resp->getStatus(),
            'provider' => $resp->getProvider(),
            'payment_id' => $resp->getPaymentId(),
            'successful' => $resp->isSuccessful(),
            'payment_updated' => $payment !== null,
        ], 'messages.payment.confirmed');
    }

    public function show(Payment $payment): JsonResponse
    {
        return $this->successResponse(new PaymentResource($payment), 'messages.payment.fetched');
    }
}
