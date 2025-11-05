@extends('admin.master')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">المدفوعات</h1>
  <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 mb-3">
    <div class="col-md-2">
      <select name="status" class="form-select">
        <option value="">كل الحالات</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>ناجحة</option>
        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="gateway" class="form-select">
        <option value="">كل البوابات</option>
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
      <input type="number" step="0.01" name="min_amount" class="form-control" placeholder="أدنى مبلغ" value="{{ request('min_amount') }}">
    </div>
    <div class="col-md-2">
      <input type="number" step="0.01" name="max_amount" class="form-control" placeholder="أعلى مبلغ" value="{{ request('max_amount') }}">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">تصفية</button>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>الحجز</th>
          <th>المريض</th>
          <th>الطبيب</th>
          <th>البوابة</th>
          <th>المبلغ</th>
          <th>الحالة</th>
          <th>الإجراءات</th>
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
                <i class="fas fa-eye"></i> عرض
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">لا توجد نتائج</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $payments->appends(request()->query())->links() }}
</div>
@endsection

