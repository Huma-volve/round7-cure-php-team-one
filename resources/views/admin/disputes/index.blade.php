@extends('admin.master')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">النزاعات</h1>

  <ul class="nav nav-tabs mb-3" id="disputesTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">نزاعات المدفوعات</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button" role="tab">نزاعات الحجوزات</button>
    </li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="payments" role="tabpanel">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>السبب</th>
              <th>الحالة</th>
              <th>الحجز</th>
            </tr>
          </thead>
          <tbody id="payment-disputes-body"></tbody>
        </table>
      </div>
    </div>
    <div class="tab-pane fade" id="bookings" role="tabpanel">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>النوع</th>
              <th>الحالة</th>
              <th>الحجز</th>
            </tr>
          </thead>
          <tbody id="booking-disputes-body"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function loadPaymentDisputes() {
  const res = await fetch('/api/admin/disputes/payments', { credentials: 'same-origin' });
  const data = await res.json();
  const tbody = document.getElementById('payment-disputes-body');
  tbody.innerHTML = '';
  (data.data || []).forEach(d => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${d.id}</td><td>${d.reason||'-'}</td><td>${d.status}</td><td>${d.payment?.booking_id||'-'}</td>`;
    tbody.appendChild(tr);
  });
}
async function loadBookingDisputes() {
  const res = await fetch('/api/admin/disputes/bookings', { credentials: 'same-origin' });
  const data = await res.json();
  const tbody = document.getElementById('booking-disputes-body');
  tbody.innerHTML = '';
  (data.data || []).forEach(d => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${d.id}</td><td>${d.type}</td><td>${d.status}</td><td>${d.booking_id}</td>`;
    tbody.appendChild(tr);
  });
}
loadPaymentDisputes();
loadBookingDisputes();
</script>
@endpush

