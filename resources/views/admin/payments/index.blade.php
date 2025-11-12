@extends('admin.master')

@section('title', __('payments.Payments'))

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">{{ __('payments.Payments') }}</h1>
  <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 mb-3">
    <div class="col-md-2">
      <select name="status" class="form-select">
        <option value="">{{ __('payments.All Statuses') }}</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('payments.Pending') }}</option>
        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>{{ __('payments.Success') }}</option>
        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('payments.Failed') }}</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="gateway" class="form-select">
        <option value="">{{ __('payments.All Gateways') }}</option>
        <option value="cash" {{ request('gateway') == 'cash' ? 'selected' : '' }}>Cash</option>
        <option value="stripe" {{ request('gateway') == 'stripe' ? 'selected' : '' }}>Stripe</option>
        <option value="paypal" {{ request('gateway') == 'paypal' ? 'selected' : '' }}>PayPal</option>
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="col-md-2">
      <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="col-md-2">
      <input type="number" step="0.01" name="min_amount" class="form-control" placeholder="{{ __('payments.Min Amount') }}" value="{{ request('min_amount') }}">
    </div>
    <div class="col-md-2">
      <input type="number" step="0.01" name="max_amount" class="form-control" placeholder="{{ __('payments.Max Amount') }}" value="{{ request('max_amount') }}">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">{{ __('payments.Filter') }}</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('payments.Booking') }}</th>
          <th>{{ __('payments.Patient') }}</th>
          <th>{{ __('payments.Doctor') }}</th>
          <th>{{ __('payments.Gateway') }}</th>
          <th>{{ __('payments.Amount') }}</th>
          <th>{{ __('payments.Status') }}</th>
          <th>{{ __('payments.Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payments as $payment)
          <tr>
            <td>{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}</td>
            <td>
              <a href="{{ route('admin.bookings.show', $payment->booking_id) }}">#{{ $payment->booking_id }}</a>
            </td>
            <td>{{ $payment->booking->patient->user->name ?? '-' }}</td>
            <td>{{ $payment->booking->doctor->user->name ?? '-' }}</td>
            <td>{{ $payment->gateway }}</td>
            <td>{{ $payment->amount }} {{ $payment->currency ?? 'EGP' }}</td>
            <td>
              <span class="badge badge-{{ $payment->status == 'success' ? 'success' : ($payment->status == 'failed' ? 'danger' : 'warning') }}">
                {{ $payment->status }}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> {{ __('payments.View') }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">{{ __('payments.No Results') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $payments->appends(request()->query())->links() }}
</div>
@endsection
