@extends('admin.master')
@section('title', 'تفاصيل الدفع')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">  {{ __('payments.Payment Details') }} </h1>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> {{ __('payments.Back') }}
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{__('payments.Payment Information')}}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong> {{ __('payments.Payment ID') }}:</strong></div>
                        <div class="col-md-8">#{{ $payment->id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong> {{__('payments.Booking ID')}}:</strong></div>
                        <div class="col-md-8">
                            <a href="{{ route('admin.bookings.show', $payment->booking_id) }}">#{{ $payment->booking_id }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong> {{__('payments.Amount')}}:</strong></div>
                        <div class="col-md-8"><strong>{{ $payment->amount }} {{ $payment->currency ?? 'EGP' }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong> {{__('payments.Gateway')}}:</strong></div>
                        <div class="col-md-8">{{ $payment->gateway ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>  {{__('payments.Transaction ID')}} :</strong></div>
                        <div class="col-md-8">{{ $payment->transaction_id ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong> {{__('payments.Status')}}</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $payment->status == 'success' ? 'success' : ($payment->status == 'failed' ? 'danger' : 'warning') }}">
                                {{ $payment->status }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>  {{__('payments.Payment Date')}}</strong></div>
                        <div class="col-md-8">{{ $payment->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($payment->booking)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{__('payments.Booking Information')}}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{__('payments.Doctor')}}:</strong></div>
                        <div class="col-md-8">
                            {{ $payment->booking->doctor->user->name ?? '-' }}
                            @if($payment->booking->doctor->user)
                                <a href="{{ route('admin.users.show', $payment->booking->doctor->user->id) }}" class="btn btn-sm btn-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{__('payments.Patient')}}:</strong></div>
                        <div class="col-md-8">
                            {{ $payment->booking->patient->user->name ?? '-' }}
                            @if($payment->booking->patient->user)
                                <a href="{{ route('admin.users.show', $payment->booking->patient->user->id) }}" class="btn btn-sm btn-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>    {{__('payments.Booking Date')}}:</strong></div>
                        <div class="col-md-8">{{ $payment->booking->date_time ? $payment->booking->date_time->format('Y-m-d H:i') : '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{__('payments.Booking Status')}}:</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $payment->booking->status == 'confirmed' ? 'success' : ($payment->booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                                {{ $payment->booking->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($payment->disputes && $payment->disputes->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{__('payments.Disputes')}}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('payments.Reason')}}</th>
                                    <th>{{__('payments.Status')}}</th>
                                    <th>{{__('payments.Date')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->disputes as $dispute)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.disputes.index') }}">#{{ $dispute->id }}</a>
                                        </td>
                                        <td>{{ $dispute->reason ?? '-' }}</td>
                                        <td>{{ $dispute->status }}</td>
                                        <td>{{ $dispute->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if($payment->status === 'success')
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-primary">{{__('payments.Refund Amount')}}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payments.refund', $payment->id) }}" onsubmit="return confirm('هل أنت متأكد من استرداد هذا المبلغ؟');">
                        @csrf
                        <div class="form-group">
                            <label for="reason">{{__('payments.Refund Reason')}}</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" required placeholder="{{__('payments.Enter refund reason...')}}"></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-undo"></i> {{ __('payments.Request Refund') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

