@extends('admin.master')
@section('title', __('حجوزاتي'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('حجوزاتي') }}</h1>
        <span class="text-muted">{{ $doctor->user->name }}</span>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">{{ __('كل الحالات') }}</option>
                @foreach(['pending', 'confirmed', 'cancelled', 'rescheduled'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ __($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">{{ __('تصفية') }}</button>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('المريض') }}</th>
                            <th>{{ __('التاريخ/الوقت') }}</th>
                            <th>{{ __('الحالة') }}</th>
                            <th>{{ __('السعر') }}</th>
                            <th>{{ __('الإجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>{{ $booking->patient->user->name ?? '-' }}</td>
                                <td>{{ $booking->date_time?->format('Y-m-d H:i') }}</td>
                                <td>
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
                                </td>
                                <td>{{ number_format($booking->price, 2) }} {{ __('ريال') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('doctor.bookings.show', $booking->id) }}" class="btn btn-sm btn-info" title="{{ __('عرض التفاصيل') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('doctor.bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="{{ __('تأكيد') }}" onclick="return confirm('{{ __('هل أنت متأكد من تأكيد هذا الحجز؟') }}')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            @if($booking->isCancellable())
                                                <form action="{{ route('doctor.bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('إلغاء') }}" onclick="return confirm('{{ __('هل أنت متأكد من إلغاء هذا الحجز؟') }}')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        
                                        @if($booking->isReschedulable() && $booking->status !== 'cancelled')
                                            <button type="button" class="btn btn-sm btn-warning" title="{{ __('إعادة جدولة') }}" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $booking->id }}">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Reschedule Modal -->
                                    @if($booking->isReschedulable() && $booking->status !== 'cancelled')
                                    <div class="modal fade" id="rescheduleModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
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
                                                            <label for="date_time{{ $booking->id }}" class="form-label">{{ __('التاريخ والوقت الجديد') }}</label>
                                                            <input type="datetime-local" class="form-control" id="date_time{{ $booking->id }}" name="date_time" required value="{{ $booking->date_time?->format('Y-m-d\TH:i') }}">
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">{{ __('لا توجد حجوزات متاحة') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bookings->hasPages())
            <div class="card-footer">
                {{ $bookings->appends(['status' => $status])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

