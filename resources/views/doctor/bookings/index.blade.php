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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>{{ $booking->patient->user->name ?? '-' }}</td>
                                <td>{{ $booking->date_time?->format('Y-m-d H:i') }}</td>
                                <td>{{ $booking->status }}</td>
                                <td>{{ number_format($booking->price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">{{ __('لا توجد حجوزات متاحة') }}</td>
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

