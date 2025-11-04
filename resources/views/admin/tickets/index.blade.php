@extends('admin.master')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">تذاكر الدعم</h1>
  <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">كل الحالات</option>
        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المعالجة</option>
        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
      </select>
    </div>
    <div class="col-md-3">
      <select name="priority" class="form-select">
        <option value="">كل الأولويات</option>
        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">تصفية</button>
    </div>
  </form>
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
      <tbody>
        @forelse($tickets as $ticket)
          <tr>
            <td>{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}</td>
            <td>{{ $ticket->subject }}</td>
            <td>{{ $ticket->priority }}</td>
            <td>{{ $ticket->status }}</td>
            <td>{{ $ticket->assignedAdmin->name ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">لا توجد نتائج</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $tickets->appends(request()->query())->links() }}
</div>
@endsection

