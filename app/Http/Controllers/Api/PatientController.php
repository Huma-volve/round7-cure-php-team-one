<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\RescheduleBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Repositories\BookingRepository;
use App\Services\Booking\BookingService;
use App\Services\Payment\PaymentService;
use App\DTOs\Payment\CreatePaymentDTO;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private BookingService $bookingService,
        private BookingRepository $bookingRepository,
        private PaymentService $paymentService,
    ) {}

    /**
     * حجز موعد جديد
     */
    public function bookAppointment(BookAppointmentRequest $request): JsonResponse
    {
        try {
            $patient = Auth::user()->patient;
            
            if (!$patient) {
                return $this->notFoundResponse('messages.patient.not_found');
            }

            $doctor = Doctor::findOrFail($request->doctor_id);
            
            $booking = $this->bookingService->bookAppointment($patient, $doctor, [
                'date_time' => $request->date_time,
                'payment_method' => $request->payment_method,
            ]);

            $responseData = [
                'booking' => new BookingResource($booking),
            ];

            // إنشاء نية دفع تلقائياً إن لم تكن الطريقة نقداً
            if ($request->payment_method !== 'cash') {
                $currency = (string) config('app.currency', env('PAYMENT_CURRENCY', 'USD'));
                $payment = $this->paymentService->create(new CreatePaymentDTO(
                    bookingId: $booking->id,
                    gateway: $request->payment_method,
                    currency: $currency,
                    amount: (string) $booking->price,
                    description: 'Booking #'.$booking->id.' with Dr. '.$doctor->user?->name,
                    patientId: Auth::id(),
                    metadata: ['booking_id' => $booking->id],
                    returnUrl: $request->input('return_url'),
                    cancelUrl: $request->input('cancel_url'),
                ));

                $responseData['payment'] = [
                    'provider' => $payment->getProvider(),
                    'payment_id' => $payment->getPaymentId(),
                    'client_secret' => $payment->getClientSecret(), // Stripe فقط
                    'approve_url' => $payment->getApproveUrl(),     // PayPal فقط
                    'status' => $payment->getStatus(),
                ];
            }

            return $this->createdResponse(
                $responseData,
                'messages.booking.created'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * عرض جميع مواعيد المريض
     */
    public function myBookings(Request $request): JsonResponse
    {
        try {
            $patient = Auth::user()->patient;
            
            if (!$patient) {
                return $this->notFoundResponse('messages.patient.not_found');
            }

            $bookings = $this->bookingRepository->getPatientBookings($patient->id, [
                'status' => $request->status,
                'upcoming_only' => $request->boolean('upcoming_only'),
                'date' => $request->date,
            ]);

            return $this->paginatedResponse(
                BookingResource::collection($bookings)->response()->getData(true),
                'messages.booking.fetched'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * عرض تفاصيل موعد
     */
    public function show($id): JsonResponse
    {
        try {
            $booking = $this->bookingRepository->findByIdWithRelations($id);
            
            if (!$booking) {
                return $this->notFoundResponse('messages.booking.not_found');
            }

            $patient = Auth::user()->patient;
            
            if ($booking->patient_id != $patient->id && !Auth::user()->hasRole('admin')) {
                return $this->unauthorizedResponse('messages.booking.unauthorized');
            }

            return $this->successResponse(
                new BookingResource($booking),
                'messages.booking.details_fetched'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * إعادة جدولة موعد
     */
    public function reschedule(RescheduleBookingRequest $request, $id): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($id);
            $patient = Auth::user()->patient;

            if ($booking->patient_id != $patient->id) {
                return $this->unauthorizedResponse('messages.booking.not_yours');
            }

            $booking = $this->bookingService->rescheduleBooking($booking, $request->date_time);

            return $this->successResponse(
                new BookingResource($booking),
                'messages.booking.rescheduled'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * إلغاء موعد
     */
    public function cancel($id): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($id);
            $patient = Auth::user()->patient;

            if ($booking->patient_id != $patient->id) {
                return $this->unauthorizedResponse('messages.booking.not_yours');
            }

            $booking = $this->bookingService->cancelBooking($booking);

            return $this->successResponse(
                new BookingResource($booking),
                'messages.booking.cancelled'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * معالجة الأخطاء
     */
    private function handleException(\Exception $e): JsonResponse
    {
        $statusCode = $e->getCode() && $e->getCode() >= 400 && $e->getCode() < 600 
            ? $e->getCode() 
            : 500;

        return match($statusCode) {
            409 => $this->conflictResponse($e->getMessage()),
            404 => $this->notFoundResponse($e->getMessage()),
            403 => $this->unauthorizedResponse($e->getMessage()),
            default => $this->serverErrorResponse('messages.operation_error', $e->getMessage())
        };
    }
}

