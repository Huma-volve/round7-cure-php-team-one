@extends('admin.master')
@section('title', __('لوحة الطبيب'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('لوحة الطبيب') }}</h1>
        <span class="text-muted">{{ $doctor->user->name }}</span>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('إجمالي الحجوزات') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_bookings'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('الحجوزات القادمة') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['upcoming_bookings'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('المرضى') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['patients_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('إجمالي الأرباح') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['earnings'], 2) }} {{ __('ج.م') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('آخر الحجوزات') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('المريض') }}</th>
                            <th>{{ __('التاريخ/الوقت') }}</th>
                            <th>{{ __('الحالة') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->patient->user->name ?? '-' }}</td>
                                <td>{{ $booking->date_time?->format('Y-m-d H:i') }}</td>
                                <td>{{ $booking->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">{{ __('لا توجد حجوزات.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

