<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\StorePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Services\Payment\PaymentMethodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PatientPaymentMethodController extends Controller
{
    public function __construct(private readonly PaymentMethodService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $methods = $request->user()->paymentMethods()
            ->whereNull('deleted_at')
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'message' => __('messages.payment_method.list'),
            'data' => PaymentMethodResource::collection($methods),
        ]);
    }

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $data = $request->validated();
        $attributes = $this->buildPaymentAttributes($request, $data);

        $method = $this->service->create($request->user(), $attributes, $request->user());

        return response()->json([
            'status' => true,
            'message' => __('messages.payment_method.created'),
            'data' => new PaymentMethodResource($method),
        ], 201);
    }

    public function setDefault(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        Gate::authorize('update', $paymentMethod);

        $method = $this->service->setDefault($paymentMethod, $request->user());

        return response()->json([
            'status' => true,
            'message' => __('messages.payment_method.set_default'),
            'data' => new PaymentMethodResource($method),
        ]);
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        Gate::authorize('delete', $paymentMethod);

        $this->service->delete($paymentMethod, $request->user());

        return response()->json([
            'status' => true,
            'message' => __('messages.payment_method.deleted'),
        ]);
    }

    protected function buildPaymentAttributes(Request $request, array $data): array
    {
        $last4 = substr($data['card_number'], -4);

        return [
            'provider' => 'card',
            'brand' => $data['brand'] ?? 'VISA',
            'last4' => $last4,
            'exp_month' => $data['exp_month'],
            'exp_year' => $data['exp_year'],
            'gateway' => $data['gateway'] ?? 'stripe',
            'token' => $this->generateToken($request->user()->id, $data['card_number']),
            'is_default' => $request->boolean('is_default'),
            'metadata' => [
                'cardholder_name' => $data['cardholder_name'],
                'masked_card' => $this->maskCard($data['card_number']),
            ],
        ];
    }

    protected function maskCard(string $cardNumber): string
    {
        return str_repeat('*', max(0, strlen($cardNumber) - 4)) . substr($cardNumber, -4);
    }

    protected function generateToken(int $userId, string $cardNumber): string
    {
        return hash('sha256', $userId . '|' . $cardNumber . '|' . Str::uuid()->toString());
    }
}

