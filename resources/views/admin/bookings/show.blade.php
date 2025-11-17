@extends('admin.master')
@section('title', __('bookings.booking_info'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('bookings.booking_info') }}</h1>
        <div>
            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> {{ __('bookings.edit') }}
            </a>
        

            <a href="{{ $backUrl ?? route('admin.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> {{ __('bookings.return') }}

            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('bookings.booking_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.booking_number') }}:</strong></div>
                        <div class="col-md-8">#{{ $booking->id }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.doctor') }}:</strong></div>
                        <div class="col-md-8">
                            {{ $booking->doctor->user->name ?? '-' }}
                            @if($booking->doctor->user)
                                <a href="{{ route('admin.users.show', $booking->doctor->user->id) }}" class="btn btn-sm btn-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.patient') }}:</strong></div>
                        <div class="col-md-8">
                            {{ $booking->patient->user->name ?? '-' }}
                            @if($booking->patient->user)
                                <a href="{{ route('admin.users.show', $booking->patient->user->id) }}" class="btn btn-sm btn-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.booking_date') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->date_time ? $booking->date_time->format('Y-m-d H:i') : '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.status') }}:</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                                {{ __('bookings.' . $booking->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.price') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->price ?? '-' }} {{ $booking->price ? 'EGP' : '' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.payment_method') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->payment_method ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.created_at') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($booking->payment)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('bookings.payment_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.payment_number') }}:</strong></div>
                        <div class="col-md-8">
                            <a href="{{ route('admin.payments.show', $booking->payment->id) }}">#{{ $booking->payment->id }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.price') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->payment->amount }} {{ $booking->payment->currency ?? 'EGP' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.gateway') }}:</strong></div>
                        <div class="col-md-8">{{ $booking->payment->gateway ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('bookings.status') }}:</strong></div>
                        <div class="col-md-8">
                            <span class="badge badge-{{ $booking->payment->status == 'success' ? 'success' : ($booking->payment->status == 'failed' ? 'danger' : 'warning') }}">
                                {{ $booking->payment->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($booking->disputes && $booking->disputes->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('bookings.disputes') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('bookings.dispute_type') }}</th>
                                    <th>{{ __('bookings.dispute_status') }}</th>
                                    <th>{{ __('bookings.dispute_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->disputes as $dispute)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $dispute->type }}</td>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('bookings.change_status') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking->id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="status">{{ __('bookings.new_status') }}</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>{{ __('bookings.pending') }}</option>
                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>{{ __('bookings.confirmed') }}</option>
                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>{{ __('bookings.cancelled') }}</option>
                                <option value="rescheduled" {{ $booking->status == 'rescheduled' ? 'selected' : '' }}>{{ __('bookings.rescheduled') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('bookings.update_status') }}</button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">{{ __('bookings.delete_booking') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('bookings.delete_warning') }}</p>
                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('{{ __('bookings.confirm_delete') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> {{ __('bookings.delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
