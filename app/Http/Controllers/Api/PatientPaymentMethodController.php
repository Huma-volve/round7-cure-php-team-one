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
        $method = $this->service->create($request->user(), $request->validated(), $request->user());

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
}

