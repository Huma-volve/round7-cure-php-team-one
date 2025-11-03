@extends('admin.master')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">المستخدمون</h1>
  <div id="users-app">
    <div class="mb-3">
      <input type="text" id="q" class="form-control" placeholder="بحث بالاسم أو البريد">
    </div>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>الاسم</th>
            <th>البريد</th>
            <th>تاريخ الإنشاء</th>
          </tr>
        </thead>
        <tbody id="users-body"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function loadUsers() {
  const q = document.getElementById('q').value;
  const params = new URLSearchParams();
  if (q) params.append('q', q);
  const res = await fetch('/api/admin/users?' + params.toString(), { credentials: 'same-origin' });
  const data = await res.json();
  const tbody = document.getElementById('users-body');
  tbody.innerHTML = '';
  (data.data || []).forEach(u => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${u.id}</td><td>${u.name||'-'}</td><td>${u.email||'-'}</td><td>${u.created_at||'-'}</td>`;
    tbody.appendChild(tr);
  });
}
document.getElementById('q').addEventListener('input', () => loadUsers());
loadUsers();
</script>
@endpush

