@extends('admin.master')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">المدفوعات</h1>
  <div class="row g-2 mb-3">
    <div class="col-md-2">
      <select id="status" class="form-select">
        <option value="">كل الحالات</option>
        <option value="pending">قيد الانتظار</option>
        <option value="success">ناجحة</option>
        <option value="failed">فاشلة</option>
      </select>
    </div>
    <div class="col-md-2">
      <select id="gateway" class="form-select">
        <option value="">كل البوابات</option>
        <option value="cash">Cash</option>
        <option value="stripe">Stripe</option>
        <option value="paypal">PayPal</option>
      </select>
    </div>
    <div class="col-md-2"><input type="date" id="date_from" class="form-control"></div>
    <div class="col-md-2"><input type="date" id="date_to" class="form-control"></div>
    <div class="col-md-2"><input type="number" step="0.01" id="min_amount" class="form-control" placeholder="أدنى مبلغ"></div>
    <div class="col-md-2"><input type="number" step="0.01" id="max_amount" class="form-control" placeholder="أعلى مبلغ"></div>
  </div>
  <div class="mb-3"><button class="btn btn-primary" onclick="loadPayments()">تصفية</button></div>
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
        </tr>
      </thead>
      <tbody id="payments-body"></tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function loadPayments() {
  const p = new URLSearchParams();
  const status = document.getElementById('status').value;
  const gateway = document.getElementById('gateway').value;
  const df = document.getElementById('date_from').value;
  const dt = document.getElementById('date_to').value;
  const min = document.getElementById('min_amount').value;
  const max = document.getElementById('max_amount').value;
  if (status) p.append('status', status);
  if (gateway) p.append('gateway', gateway);
  if (df) p.append('date_from', df);
  if (dt) p.append('date_to', dt);
  if (min) p.append('min_amount', min);
  if (max) p.append('max_amount', max);
  const res = await fetch('/api/admin/payments?' + p.toString(), { credentials: 'same-origin' });
  const data = await res.json();
  const tbody = document.getElementById('payments-body');
  tbody.innerHTML = '';
  (data.data || []).forEach(pay => {
    const tr = document.createElement('tr');
    const patient = pay.booking?.patient?.user?.name || '-';
    const doctor = pay.booking?.doctor?.user?.name || '-';
    tr.innerHTML = `<td>${pay.id}</td><td>${pay.booking_id}</td><td>${patient}</td><td>${doctor}</td><td>${pay.gateway}</td><td>${pay.amount}</td><td>${pay.status}</td>`;
    tbody.appendChild(tr);
  });
}
loadPayments();
</script>
@endpush

