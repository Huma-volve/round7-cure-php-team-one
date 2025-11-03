@extends('admin.master')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">الحجوزات</h1>
  <div class="row g-2 mb-3">
    <div class="col-md-3">
      <select id="status" class="form-select">
        <option value="">كل الحالات</option>
        <option value="pending">قيد الانتظار</option>
        <option value="confirmed">مؤكد</option>
        <option value="cancelled">ملغى</option>
        <option value="rescheduled">معاد جدولته</option>
      </select>
    </div>
    <div class="col-md-3"><input type="date" id="date_from" class="form-control"></div>
    <div class="col-md-3"><input type="date" id="date_to" class="form-control"></div>
    <div class="col-md-3"><button class="btn btn-primary w-100" onclick="loadBookings()">تصفية</button></div>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>الطبيب</th>
          <th>المريض</th>
          <th>الوقت</th>
          <th>الحالة</th>
        </tr>
      </thead>
      <tbody id="bookings-body"></tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function loadBookings() {
  const params = new URLSearchParams();
  const status = document.getElementById('status').value;
  const df = document.getElementById('date_from').value;
  const dt = document.getElementById('date_to').value;
  if (status) params.append('status', status);
  if (df) params.append('date_from', df);
  if (dt) params.append('date_to', dt);
  const res = await fetch('/api/admin/bookings?' + params.toString(), { credentials: 'same-origin' });
  const data = await res.json();
  const tbody = document.getElementById('bookings-body');
  tbody.innerHTML = '';
  (data.data || []).forEach(b => {
    const tr = document.createElement('tr');
    const doctor = b.doctor?.user?.name || '-';
    const patient = b.patient?.user?.name || '-';
    tr.innerHTML = `<td>${b.id}</td><td>${doctor}</td><td>${patient}</td><td>${b.date_time||'-'}</td><td>${b.status}</td>`;
    tbody.appendChild(tr);
  });
}
loadBookings();
</script>
@endpush

