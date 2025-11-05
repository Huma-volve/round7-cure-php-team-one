@extends('admin.master')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">المستخدمون</h1>
  <div class="mb-3">
    <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex">
      <input type="text" name="q" class="form-control me-2" placeholder="بحث بالاسم أو البريد" value="{{ request('q') }}">
      <button type="submit" class="btn btn-primary">بحث</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>البريد</th>
          <th>تاريخ الإنشاء</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
            <td>{{ $user->name ?? '-' }}</td>
            <td>{{ $user->email ?? '-' }}</td>
            <td>{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : '-' }}</td>
            <td>
              <div class="btn-group" role="group">
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">
                  <i class="fas fa-eye"></i> عرض
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                  <i class="fas fa-edit"></i> تعديل
                </a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <i class="fas fa-trash"></i> حذف
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">لا توجد نتائج</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $users->appends(request()->query())->links() }}
</div>
@endsection

