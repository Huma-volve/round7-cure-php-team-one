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

        return $this->createdResponse(new PaymentResource($payment), 'تم إنشاء عملية الدفع');
    }

    public function confirm(ConfirmPaymentRequest $request): JsonResponse
    {
        $resp = $this->payments->confirm($request->gateway, $request->payment_id, []);
        return $this->successResponse([
            'status' => $resp->getStatus(),
            'provider' => $resp->getProvider(),
            'payment_id' => $resp->getPaymentId(),
        ], 'تم تأكيد عملية الدفع');
    }

    public function show(Payment $payment): JsonResponse
    {
        return $this->successResponse(new PaymentResource($payment), 'تم جلب تفاصيل الدفع');
    }
}
