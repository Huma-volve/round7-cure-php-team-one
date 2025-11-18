@extends('admin.master')
@section('title', __('تفاصيل الحجز'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('تفاصيل الحجز') }}</h1>
        <a href="{{ route('doctor.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('رجوع') }}
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('معلومات الحجز') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('رقم الحجز') }}:</strong></div>
                        <div class="col-md-8">#{{ $booking->id }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('المريض') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->patient->user->name ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('التاريخ والوقت') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->date_time?->format('Y-m-d H:i') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('الحالة') }}:</strong></div>
                        <div class="col-md-8">
                            @if($booking->status === 'pending')
                                <span class="badge badge-warning">{{ __('معلق') }}</span>
                            @elseif($booking->status === 'confirmed')
                                <span class="badge badge-success">{{ __('مؤكد') }}</span>
                            @elseif($booking->status === 'cancelled')
                                <span class="badge badge-danger">{{ __('ملغي') }}</span>
                            @elseif($booking->status === 'rescheduled')
                                <span class="badge badge-info">{{ __('معاد جدولته') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ $booking->status }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('السعر') }}:</strong></div>
                        <div class="col-md-8">{{ number_format($booking->price, 2) }} {{ __('ريال') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('طريقة الدفع') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->payment_method ?? '-' }}</div>
                    </div>

                    @if($booking->payment)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('حالة الدفع') }}:</strong></div>
                        <div class="col-md-8">
                            @if($booking->payment->status === 'completed')
                                <span class="badge badge-success">{{ __('مكتمل') }}</span>
                            @elseif($booking->payment->status === 'pending')
                                <span class="badge badge-warning">{{ __('معلق') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('فاشل') }}</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('تاريخ الإنشاء') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('الإجراءات') }}</h6>
                </div>
                <div class="card-body">
                    @if($booking->status === 'pending')
                        <form action="{{ route('doctor.bookings.confirm', $booking->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('{{ __('هل أنت متأكد من تأكيد هذا الحجز؟') }}')">
                                <i class="fas fa-check"></i> {{ __('تأكيد الحجز') }}
                            </button>
                        </form>
                        
                        @if($booking->isCancellable())
                        <form action="{{ route('doctor.bookings.cancel', $booking->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('{{ __('هل أنت متأكد من إلغاء هذا الحجز؟') }}')">
                                <i class="fas fa-times"></i> {{ __('إلغاء الحجز') }}
                            </button>
                        </form>
                        @endif
                    @endif
                    
                    @if($booking->isReschedulable() && $booking->status !== 'cancelled')
                        <button type="button" class="btn btn-warning btn-block" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                            <i class="fas fa-calendar-alt"></i> {{ __('إعادة جدولة') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
@if($booking->isReschedulable() && $booking->status !== 'cancelled')
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('doctor.bookings.reschedule', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('إعادة جدولة الحجز') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="date_time" class="form-label">{{ __('التاريخ والوقت الجديد') }}</label>
                        <input type="datetime-local" class="form-control" id="date_time" name="date_time" required value="{{ $booking->date_time?->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('حفظ') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

