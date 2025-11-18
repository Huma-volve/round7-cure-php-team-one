@extends('admin.master')
@section('title', __('مدفوعاتي'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('مدفوعاتي') }}</h1>
        <span class="text-muted">{{ $doctor->user->name }}</span>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('المريض') }}</th>
                            <th>{{ __('حجز رقم') }}</th>
                            <th>{{ __('المبلغ') }}</th>
                            <th>{{ __('البوابة') }}</th>
                            <th>{{ __('الحالة') }}</th>
                            <th>{{ __('التاريخ') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->booking->patient->user->name ?? '-' }}</td>
                                <td>#{{ $payment->booking_id }}</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ strtoupper($payment->gateway) }}</td>
                                <td>{{ $payment->status }}</td>
                                <td>{{ $payment->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">{{ __('لا توجد مدفوعات') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
            <div class="card-footer">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

