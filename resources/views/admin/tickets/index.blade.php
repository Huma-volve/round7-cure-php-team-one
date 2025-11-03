@extends('admin.master')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">تذاكر الدعم</h1>
  <div class="row g-2 mb-3">
    <div class="col-md-3">
      <select id="status" class="form-select">
        <option value="">كل الحالات</option>
        <option value="open">مفتوحة</option>
        <option value="pending">قيد المعالجة</option>
        <option value="closed">مغلقة</option>
      </select>
    </div>
    <div class="col-md-3">
      <select id="priority" class="form-select">
        <option value="">كل الأولويات</option>
        <option value="low">منخفضة</option>
        <option value="medium">متوسطة</option>
        <option value="high">عالية</option>
      </select>
    </div>
    <div class="col-md-3"><button class="btn btn-primary w-100" onclick="loadTickets()">تصفية</button></div>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>الموضوع</th>
          <th>الأولوية</th>
          <th>الحالة</th>
          <th>المعيّن</th>
        </tr>
      </thead>
      <tbody id="tickets-body"></tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function loadTickets() {
  const p = new URLSearchParams();
  const status = document.getElementById('status').value;
  const priority = document.getElementById('priority').value;
  if (status) p.append('status', status);
  if (priority) p.append('priority', priority);
  const res = await fetch('/api/admin/tickets?' + p.toString(), { credentials: 'same-origin' });
  const data = await res.json();
  const tbody = document.getElementById('tickets-body');
  tbody.innerHTML = '';
  (data.data || []).forEach(t => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${t.id}</td><td>${t.subject}</td><td>${t.priority}</td><td>${t.status}</td><td>${t.assigned_admin_id||'-'}</td>`;
    tbody.appendChild(tr);
  });
}
loadTickets();
</script>
@endpush

